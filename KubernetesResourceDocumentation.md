
# Namespaces

```yaml
apiVersion: v1
kind: Namespace
metadata:
  name: example-namespace
  labels:
    environment: production
    team: backend
  annotations:
    description: This is an example namespace for the production environment
    owner: John Doe
    created_at: 2022-05-01T12:00:00Z
spec:
  finalizers:
    - kubernetes
status:
  phase: Active
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (Namespace).
3. **metadata**: Contains metadata about the Namespace, including its name, labels, and annotations.
    - **name**: The name of the Namespace.
    - **labels**: Key-value pairs used to identify and select Namespaces. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the Namespace. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **spec**: Defines the specification of the Namespace.
    - **finalizers**: An array of strings specifying the finalizers that must be run before the Namespace can be removed from the system. Finalizers are executed during Namespace deletion to perform cleanup tasks.
5. **status**: Provides information about the current status of the Namespace.
    - **phase**: Indicates the current phase of the Namespace (e.g., Active, Terminating). In this example, the Namespace is in the Active phase, meaning it is operational and available for use.
# Pods

```yaml
apiVersion: v1
kind: Pod
metadata:
  name: example-pod
  namespace: default
  labels:
    app: nginx
    environment: production
  annotations:
    description: This is an example Pod running nginx
spec:
  containers:
  - name: nginx-container
    image: nginx:latest
    ports:
    - containerPort: 80
    resources:
      requests:
        memory: "64Mi"
        cpu: "250m"
      limits:
        memory: "128Mi"
        cpu: "500m"
    env:
    - name: ENV_VAR_NAME
      value: "value"
    - name: ENV_VAR_NAME
      value: "value"
    volumeMounts:
    - name: example-volume
      mountPath: /data
  volumes:
  - name: example-volume
    emptyDir: {}
  restartPolicy: Always
  terminationGracePeriodSeconds: 30
  affinity:
    nodeAffinity:
      requiredDuringSchedulingIgnoredDuringExecution:
        nodeSelectorTerms:
        - matchExpressions:
          - key: example-key
            operator: In
            values:
            - example-value
  tolerations:
  - key: example-toleration-key
    operator: Equal
    value: example-toleration-value
    effect: NoSchedule
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (Pod).
3. **metadata**: Contains metadata about the Pod, including its name, namespace, labels, and annotations.
    - **name**: The name of the Pod.
    - **namespace**: The namespace in which the Pod resides.
    - **labels**: Key-value pairs used to identify and select Pods. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the Pod. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **spec**: Defines the specification of the Pod.
    - **containers**: Specifies the containers that run in the Pod.
        - **name**: The name of the container.
        - **image**: The Docker image to run in the container.
        - **ports**: Specifies ports to open on the container.
        - **resources**: Specifies the resource requests and limits for the container (e.g., CPU and memory).
        - **env**: Specifies environment variables to set in the container.
        - **volumeMounts**: Mounts volumes into the container's filesystem.
    - **volumes**: Specifies volumes that can be mounted into containers.
    - **restartPolicy**: Specifies the restart policy for the Pod (e.g., Always, OnFailure, Never).
    - **terminationGracePeriodSeconds**: Specifies the grace period for terminating the Pod.
    - **affinity**: Specifies affinity and anti-affinity rules for the Pod.
    - **tolerations**: Specifies tolerations for the Pod, allowing it to be scheduled on nodes with specific taints.

# Deployments

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: example-deployment
  namespace: default
  labels:
    app: nginx
    environment: production
  annotations:
    description: This is an example Deployment managing nginx Pods
spec:
  replicas: 3
  selector:
    matchLabels:
      app: nginx
  template:
    metadata:
      labels:
        app: nginx
    spec:
      containers:
      - name: nginx-container
        image: nginx:latest
        ports:
        - containerPort: 80
        resources:
          requests:
            memory: "64Mi"
            cpu: "250m"
          limits:
            memory: "128Mi"
            cpu: "500m"
        env:
        - name: ENV_VAR_NAME
          value: "value"
        volumeMounts:
        - name: example-volume
          mountPath: /data
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxUnavailable: 1
      maxSurge: 1
  minReadySeconds: 30
  revisionHistoryLimit: 5
  progressDeadlineSeconds: 600
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (Deployment).
3. **metadata**: Contains metadata about the Deployment, including its name, namespace, labels, and annotations.
    - **name**: The name of the Deployment.
    - **namespace**: The namespace in which the Deployment resides.
    - **labels**: Key-value pairs used to identify and select Deployments. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the Deployment. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **spec**: Defines the specification of the Deployment.
    - **replicas**: Specifies the desired number of replicas (Pods) for the Deployment.
    - **selector**: Specifies the labels used to select the Pods managed by the Deployment.
    - **template**: Defines the template for creating Pods managed by the Deployment.
        - **metadata**: Contains labels for Pods created from this template.
        - **spec**: Specifies the specification of Pods created from this template, including containers, volumes, and other settings.
    - **strategy**: Specifies the deployment strategy (e.g., RollingUpdate, Recreate) and parameters for updating Pods.
    - **minReadySeconds**: Specifies the minimum number of seconds for which a newly created Pod should be ready without any errors before it is considered available.
    - **revisionHistoryLimit**: Specifies the number of old ReplicaSets to retain to allow rollback.
    - **progressDeadlineSeconds**: Specifies the maximum time in seconds for a Deployment to make progress before it is considered failed.
# Services

```yaml
apiVersion: v1
kind: Service
metadata:
  name: example-service
  namespace: default
  labels:
    app: nginx
    environment: production
  annotations:
    description: This is an example Service exposing nginx Pods
spec:
  selector:
    app: nginx
  ports:
  - name: http
    protocol: TCP
    port: 80
    targetPort: 8080
    nodePort: 30000
  type: NodePort
  externalTrafficPolicy: Cluster
  sessionAffinity: None
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (Service).
3. **metadata**: Contains metadata about the Service, including its name, namespace, labels, and annotations.
    - **name**: The name of the Service.
    - **namespace**: The namespace in which the Service resides.
    - **labels**: Key-value pairs used to identify and select Services. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the Service. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **spec**: Defines the specification of the Service.
    - **selector**: Specifies the labels used to select the Pods to which traffic should be routed.
    - **ports**: Specifies the ports that the Service exposes.
        - **name**: The name of the port.
        - **protocol**: The protocol used for the port (e.g., TCP, UDP).
        - **port**: The port number on the Service.
        - **targetPort**: The port number on the Pods to which traffic should be routed.
        - **nodePort**: The port number on the nodes where the Service is exposed (only applicable for type NodePort).
    - **type**: Specifies the type of Service (e.g., ClusterIP, NodePort, LoadBalancer).
    - **externalTrafficPolicy**: Specifies the traffic policy for external traffic to the Service (e.g., Cluster, Local).
    - **sessionAffinity**: Specifies the session affinity type for the Service (e.g., None, ClientIP).
# Ingresses

```yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: example-ingress
  namespace: default
  labels:
    app: nginx
    environment: production
  annotations:
    description: This is an example Ingress routing traffic to nginx Pods
spec:
  rules:
  - host: example.com
    http:
      paths:
      - path: /nginx
        pathType: Prefix
        backend:
          service:
            name: example-service
            port:
              number: 80
  defaultBackend:
    service:
      name: default-backend
      port:
        number: 8080
  tls:
  - hosts:
    - example.com
    secretName: example-tls-secret
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (Ingress).
3. **metadata**: Contains metadata about the Ingress, including its name, namespace, labels, and annotations.
    - **name**: The name of the Ingress.
    - **namespace**: The namespace in which the Ingress resides.
    - **labels**: Key-value pairs used to identify and select Ingresses. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the Ingress. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **spec**: Defines the specification of the Ingress.
    - **rules**: Specifies the rules for routing traffic to backend Services.
        - **host**: The hostname for which the Ingress applies.
        - **http**: Specifies HTTP-based routing rules.
            - **paths**: Specifies the paths for which the Ingress should route traffic.
                - **path**: The path pattern for the routing rule.
                - **pathType**: Specifies the type of path matching (e.g., Prefix, Exact).
                - **backend**: Specifies the backend Service to which traffic should be routed.
    - **defaultBackend**: Specifies the default backend Service to which traffic should be routed if no rules match.
    - **tls**: Specifies TLS configuration for the Ingress.
        - **hosts**: The hostnames for which TLS should be enabled.
        - **secretName**: The name of the TLS secret containing the certificate and private key.

# ConfigMap

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: example-configmap
  namespace: default
  labels:
    app: my-app
  annotations:
    description: This is an example ConfigMap
data:
  config.yaml: |
    key1: value1
    key2: value2
binaryData:
  binary-file.bin: <base64-encoded-binary-data>
immutable: false
```

1. **apiVersion**: Specifies the version of the Kubernetes API that the object uses.
2. **kind**: Specifies the kind of Kubernetes object being defined (e.g., ConfigMap, Pod, Service, etc.).
3. **metadata**: Contains metadata about the ConfigMap, including its name, namespace, labels, and annotations.
    - **name**: The name of the ConfigMap.
    - **namespace**: The namespace in which the ConfigMap resides.
    - **labels**: Key-value pairs used to identify and select ConfigMaps. Labels are used for organizing and querying objects.
    - **annotations**: Additional metadata about the ConfigMap. Annotations are not used for identifying or selecting objects but can provide additional context or information.
4. **data**: Contains textual data stored as key-value pairs. Each key represents the name of a file, and the value is the content of that file.
5. **binaryData**: Similar to the `data` field, but it stores binary data instead of textual data. Binary data must be base64-encoded.
6. **immutable**: When set to true, prevents the ConfigMap from being updated. Once set to true, you cannot modify the data or binaryData fields of the ConfigMap.

# Volume types (for Pods/Deployments)

**Secret**
A volume populated by Kubernetes Secret data

```yaml
volumes:
- name: secret-volume
  secret:
    secretName: my-secret
```

**PersistentVolumeClaim**
A volume backed by a PersistentVolumeClaim (PVC), which allows Pods to access persistent storage

```yaml
volumes:
- name: pvc-volume
  persistentVolumeClaim:
    claimName: my-pvc
```

**HostPath**
A volume mounted directly from the host node's file system

```yaml
volumes:
- name: host-path-volume
  hostPath:
    path: /path/on/host
```

**NFS**
A volume mounted from a network file system (NFS) share

```yaml
volumes:
- name: nfs-volume
  nfs:
    server: nfs-server.example.com
    path: /exported/path
```

**EmptyDir**
A volume that is created when the Pod is assigned to a node and exists as long as that Pod is running on that node

```yaml
volumes:
- name: emptydir-volume
  emptyDir: {}
```

**Projected**
A volume that consists of multiple sub-volumes, including ConfigMaps, Secrets, and downward API data

```yaml
volumes:
- name: projected-volume
  projected:
    sources:
    - secret:
        name: my-secret
      mode: 0440
    - configMap:
        name: my-configmap
```

**Configmap**
A volume that uses a configured ConfigMap.

```yaml

volumes: 
- name: configmap-volume 
  configMap: 
    name: my-configmap
```