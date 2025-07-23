# Deploying Shibboleth IdP/SP Demo on Azure

This guide describes one possible way to run the demo setup from this repository on Microsoft Azure using a custom subdomain. The repository contains a Docker Compose definition that starts three containers:

* **openldap** – provides LDAP users on port `389`.
* **shibboleth-idp** – the Identity Provider on ports `443`, `8443` and `80`.
* **shibboleth-sp** – the Service Provider on ports `2080` and `2443`.

The instructions below assume you already own a DNS domain (e.g. `example.com`) where you can create sub‑domains such as `idp.example.com` or `sp.example.com`.

## Required Azure resources

1. **Resource Group** – container for all resources used in this setup.
2. **Virtual Network and Public IP** – networking for the virtual machine.
3. **Linux Virtual Machine** – host that runs Docker and Docker Compose.
4. *(Optional)* **Azure DNS Zone** – manage DNS records for your domain or sub‑domain.
5. *(Optional)* **Azure Container Registry (ACR)** – store the Docker images if you prefer building them remotely.

Running the containers on a single Linux VM is the simplest approach since the repository already includes a `docker-compose.yml` file.

## Steps

1. **Create a resource group.**
   ```bash
   az group create --name shib-demo-rg --location westeurope
   ```
2. **Provision a Linux VM.** Ubuntu LTS works well.
   ```bash
   az vm create \
     --resource-group shib-demo-rg \
     --name shib-demo-vm \
     --image UbuntuLTS \
     --size Standard_B2s \
     --public-ip-address-dns-name shib-demo \
     --admin-username <youruser> \
     --generate-ssh-keys
   ```
   Note the public IP or the Azure‐assigned DNS name `shib-demo.<region>.cloudapp.azure.com`.
3. **Open the required ports.** Allow inbound TCP ports `389`, `80`, `443`, `2080`, `2443` and `8443` on the VM.
   ```bash
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 389
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 80
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 443
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 2080
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 2443
   az vm open-port --resource-group shib-demo-rg --name shib-demo-vm --port 8443
   ```
4. **SSH into the VM** and install Docker and Docker Compose.
   ```bash
   sudo apt-get update
   sudo apt-get install -y docker.io docker-compose
   sudo usermod -aG docker $USER
   ```
5. **Clone this repository** on the VM and build the containers.
   ```bash
   git clone <repo-url> Shibboleth-SP-IDP
   cd Shibboleth-SP-IDP
   docker-compose build
   ```
6. **Adjust configuration for your domain.** Replace references to `example.org` and `example.com` with your own subdomain.
   Important files include:
   - `shibboleth-idp-dockerized/ext-conf/conf/idp.properties` (`idp.entityID`, `idp.scope`)
   - `shibboleth-idp-dockerized/ext-conf/conf/relying-party.xml`
   - `shibboleth-idp-dockerized/ext-conf/metadata/sp-example-org.xml`
   - `shibboleth-idp-dockerized/ext-conf/metadata/idp-metadata.xml`
   - `shibboleth-sp-testapp/shibboleth-sp/shibboleth2.xml`
   Update the URLs and entity IDs so they match your chosen hostnames (e.g. `https://idp.example.com`).
7. **Provide TLS certificates.** The current repository ships with self‑signed certificates for `sp.example.org` and `idp.example.com`. Replace them with certificates valid for your domain or use Let’s Encrypt. Update the paths in the configuration if certificate filenames change.
8. **Start the containers.**
   ```bash
   docker-compose up -d
   ```
9. **Configure DNS.** Create `A` or `CNAME` records so that
   - `ldap.<your-domain>`
   - `idp.<your-domain>`
   - `sp.<your-domain>`
   all resolve to the VM’s public IP or Azure DNS name. After DNS propagation you can access the service provider via `https://sp.<your-domain>:2443`.

## Alternative approaches

Instead of a VM you could deploy the containers with Azure Container Instances or Azure Kubernetes Service. In that case build the images (locally or via Azure Container Registry) and create the equivalent container group or Kubernetes manifests. A reverse proxy or ingress controller would then handle the hostnames for your subdomain.

## Summary of code changes needed

* Change every occurrence of `sp.example.org`, `idp.example.com` and `example.org` to match your chosen subdomain.
* Replace the bundled self‑signed certificates with certificates for that subdomain.
* Ensure any metadata files (`sp-example-org.xml`, `idp-metadata.xml`) reflect the new URLs and certificate names.

Once these updates are in place you can rebuild the containers and start the stack on Azure.
