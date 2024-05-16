# K8Supervisor
<p align="center">
<img width="230" height="260" align="center" src="https://github.com/freemann350/K8Supervisor/blob/main/public/img/favicon.png">
</p>

Simple K8S control panel with Laravel, using its ReST API

This project was developed using [K3S](https://k3s.io) due to its ease of use and deployment, use this [simple official guide](https://docs.k3s.io/quick-start) to quickly set up your environment.

It is **HIGHLY RECOMMENDED** to use this app with configured with authentication to the API without the use of *kubectl proxy*. Use this [official Kubernetes guide](https://kubernetes.io/docs/tasks/administer-cluster/access-cluster-api/?amp;amp#without-kubectl-proxy) to deploy the API with (TOKEN) authentication

UI Based on my previous project, [MikroKontrol](https://https://github.com/freemann350/MikroKontrol).

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
    - get
    - create
    - delete
  - Resources
    - nodes
    - namespaces
    - pods
    - deployments
    - services
    - ingresses

# ToDo

- Users
  - [x] Login
  - Edit own information
    - [x] Common information
    - [x] Password
  - [x] Create
  - [x] Read
  - [x] Update (admin cannot update another user password)
  - [x] Delete
  - [ ] Disallow admin to delete himself
- Nodes
  - [x] Index
  - [ ] Show
- Namespaces
  - [x] Index
  - [ ] Show
  - [x] Create/Store
    - [ ] Error parsing
  - [x] Delete
- Pods
  - Index
    - [x] List all
    - [ ] List by namespace (URL Query String, filters)
    - [ ] List only non-deployment pods (by default,search if metadata.ownerReferences.kind('ReplicaSet') exists, filters)
  - [ ] Show
  - [ ] Create/Store
  - [x] Delete
- Deployments
  - Index
    - [x] List all
    - [ ] List by namespace (URL Query String, filters)
  - [ ] Show
  - [ ] Create/Store
  - [x] Delete
- Services
  - Index
    - [x] List all
    - [ ] List by namespace (URL Query String, filters)
  - [ ] Show
  - [ ] Create/Store
  - [x] Delete
- Ingresses
  - Index
    - [x] List all
    - [ ] List by namespace (URL Query String, filters)
  - [ ] Show
  - [ ] Create/Store
  - [x] Delete
