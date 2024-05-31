# K8Supervisor
<p align="center">
<img width="230" height="260" align="center" src="https://github.com/freemann350/K8Supervisor/blob/main/public/img/favicon.png">
</p>

Simple K8S control panel with Laravel, using its ReST API

This project was developed using [K3S](https://k3s.io) due to its ease of use and deployment, use this [simple official guide](https://docs.k3s.io/quick-start) to quickly set up your environment.

It is **HIGHLY RECOMMENDED** to use this app with configured with authentication to the API without the use of *kubectl proxy*. Use this [official Kubernetes guide](https://kubernetes.io/docs/tasks/administer-cluster/access-cluster-api/?amp;amp#without-kubectl-proxy) to deploy the API with (TOKEN) authentication

UI Based on my previous project, [MikroKontrol](https://github.com/freemann350/MikroKontrol).

## Features:

- List Nodes
- Namespaces

  - List
  - Create
  - Delete
- Pods

  - List
  - Create
  - Delete
- Deployments

  - List
  - Create
  - Delete
- Services

  - List
  - Create
  - Delete
- Ingress

  - List
  - Create
  - Delete
- Platform roles

  - Verbs
    - Create
    - Delete
  - Resources
    - Nodes
    - Mamespaces
    - Pods
    - Deployments
    - Services
    - Ingresses
    - Custom Resources
    - Backups
## Deployment (for testing)

### K3S deployment

This project was developed using K3S as a Kubernetes flavour due to its stability, respectable performance and ease of deployment.

The development used 3 nodes on the cluster. The steps to deploy them are as follows.

1. Deploy the Master Node
```sh
curl -sfL https://get.k3s.io | sh -
```

2. Retrieve the Master Node key
```sh
cat /var/lib/rancher/k3s/server/node-token
```

3. Deploy the Worker Nodes, using the Master Node's info
```sh
curl -sfL https://get.k3s.io | K3S_URL=https://<MasterNodeIP>:6443 K3S_TOKEN=<MasterNodeToken> sh -
```

### Web App deployment

This deployment is for Debian 12/Debian based systems and uses Laravel Sail for a containerized Web & MySQL Servers

1. **Docker**

Install Docker Engine for use with Laravel Sail
```sh
apt update
apt install -y ca-certificates curl
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc

echo \
"deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
$(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
tee /etc/apt/sources.list.d/docker.list > /dev/null
apt update

apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

- Add your user to the Docker group
```sh
usermod -aG docker <YOUR_USER>
```
2. **PHP & Composer**
- Install all required PHP packages and download Composer 
```sh
apt install -y php php-fpm php-curl php-gd php-dom php-xml php-zip zip unzip

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
```
- **!!! From this point onward, do not use root !!!**

3. **Repository**

- Download this repository
```sh
git clone https://github.com/freemann350/K8Supervisor
```

4. **Laravel Sail**
- Install with composer the laravel/sail package
```sh
composer require laravel/sail --dev
```

- Install Laravel Sail using artisan (select mysql)
```sh
php artisan sail:install 
```
- (Optional) Add ./vendor/bin/sail alias to .bashrc
```sh
echo "alias sail='./vendor/bin/sail'" >> $HOME/.bashrc
```
- Start the containers
```sh
sail up -d
```
- Link the storage (used for backup creation)
```sh
sail artisan storage:link
```
- Make the DB migrations and data seeding
```sh
sail artisan migrate
sail artisan db:seed
```
5. Possible permission errors
- If there is any sort of permission problem, try running the chown command using root (alter the variables below to your user and folder)
```sh
chown -R <YOUR_USER> <K8Supervisor_FOLDER>
```
Everything should up and running now. The credentials are as follows:

Admin default account: `admin@example.com`
- Contains one cluster, DefaultCluster (`admin:123456`) via `https://192.168.50.160:6443`, using proxy access

User default account: `user@example.com`

All passwords are `password`

### Access with Proxy (Not recommended)

If you do not wish to use the API using token, use the Kubernetes console command to open a proxy to your API.
To deploy the proxy, type the following on the console command

```sh
kubectl proxy --address='0.0.0.0' --port=8001 --accept-hosts='.*'
```

This command exposes the API on all network interfaces (`--address='0.0.0.0'`), via port 8001 (`--port=8001`) and accepts any and all hosts (`--accept-hosts='.*'`).
**Keep in mind this is an unsafe practice.**

### Access with Auth Token (Recommended)

It is preferable to use a Bearer token authentication over defining a proxy to access the cluster due to the safety it provides.
For that you can follow the [Access Clusters Using the Kubernetes API](https://kubernetes.io/docs/tasks/administer-cluster/access-cluster-api/?amp;amp#without-kubectl-proxy) Kubernetes docs page to set it up.

At the time of development, this was the official configuration used:

```sh
# Check all possible clusters, as your .KUBECONFIG may have multiple contexts:
kubectl config view -o jsonpath='{"Cluster name\tServer\n"}{range .clusters[*]}{.name}{"\t"}{.cluster.server}{"\n"}{end}'

# Select name of cluster you want to interact with from above output:
export CLUSTER_NAME="some_server_name"

# Point to the API server referring the cluster name
APISERVER=$(kubectl config view -o jsonpath="{.clusters[?(@.name==\"$CLUSTER_NAME\")].cluster.server}")

# Create a secret to hold a token for the default service account
kubectl apply -f - <<EOF
apiVersion: v1
kind: Secret
metadata:
  name: default-token
  annotations:
    kubernetes.io/service-account.name: default
type: kubernetes.io/service-account-token
EOF

# Wait for the token controller to populate the secret with a token:
while ! kubectl describe secret default-token | grep -E '^token' >/dev/null; do
  echo "waiting for token..." >&2
  sleep 1
done

# Get the token value
TOKEN=$(kubectl get secret default-token -o jsonpath='{.data.token}' | base64 --decode)

# Explore the API with TOKEN
curl -X GET $APISERVER/api --header "Authorization: Bearer $TOKEN" --insecure
```

**Be sure to not forget the ClusterRoles and the ClusterRoleBindings**, the default account (at least on K3S) cannot do anything on the API.

These were the ClusterRole/ClusterRolebinding configurations used, they have full access to the API, so be mindful of that.

```yaml
#clusterrole.yml
# Create ClusterRole
# THIS ALLOWS ACCESS TO ALL RESOURCES, DO NOT USE WILLY-NILLY
apiVersion: rbac.authorization.k8s.io/v1
kind: ClusterRole
metadata:
  namespace: default
  name: default
rules:
- apiGroups: ["*"]
  resources: ["*"]
  verbs: ["*"]
```

```yaml
#clusterrolebinding.yml
apiVersion: rbac.authorization.k8s.io/v1
kind: ClusterRoleBinding
metadata:
  name: read-secrets-global
subjects:
- kind: ServiceAccount
  name: default
  apiGroup: rbac.authorization.k8s.io
roleRef:
  kind: ClusterRole
  name: default
  apiGroup: rbac.authorization.k8s.io
```

Apply these files locally, using 
```sh
kubectl apply -f clusterrole.yml
kubectl apply -f clusterrolebinding.yml
```

Don't forget to allow your firewall (if your system has one pre-configured) to permit the API port, usually **port 6443**.
