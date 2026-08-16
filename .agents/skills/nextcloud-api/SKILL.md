---
name: nextcloudapi
description: Nextcloud API Reference for integrating with Nextcloud servers
tags: [nextcloud, api, integration, webdav, ocs, login, remote-wipe, activity]
version: 1.0.0
---

## Description

Comprehensive Nextcloud API reference for integrating with Nextcloud servers. This skill provides detailed documentation for all Nextcloud Client APIs including WebDAV, OCS, Login Flow, Remote Wipe, and Activity APIs with practical examples and best practices.

## When to Use

- When you need to integrate applications with Nextcloud
- When working with Nextcloud file operations, sharing, or user management
- When implementing Nextcloud client applications
- When debugging Nextcloud API integrations
- When you need cURL examples for Nextcloud API operations

---

## Table of Contents

1. [Clients and Client APIs Overview](#clients-and-client-apis-overview)
2. [WebDAV API](#webdav-api)
3. [OCS API](#ocs-api)
4. [Login Flow](#login-flow)
5. [Remote Wipe](#remote-wipe)
6. [Activity API](#activity-api)
7. [Best Practices](#best-practices)
8. [Code Examples](#code-examples)
9. [Resources](#resources)

---

## Clients and Client APIs Overview

Nextcloud provides multiple APIs for client integration, each serving different purposes.

### API Types

| API Type | Protocol | Primary Use | Authentication |
|----------|----------|-------------|----------------|
| **WebDAV** | HTTP/HTTPS | File operations | Basic Auth, Bearer Token |
| **OCS** | HTTP/HTTPS | Nextcloud-specific features | Basic Auth, Bearer Token |
| **Login Flow** | HTTP/HTTPS | Authentication | Web-based flow |
| **Remote Wipe** | HTTP/HTTPS | Device management | Token-based |

### Base URLs

```
WebDAV: https://<server>/remote.php/dav/
OCS v1: https://<server>/ocs/v1.php/
OCS v2: https://<server>/ocs/v2.php/
```

---

## WebDAV API

WebDAV (Web Distributed Authoring and Versioning) is the primary protocol for file operations in Nextcloud, based on HTTP extensions defined in RFC 4918.

### Basic APIs

#### WebDAV Basics

**Base URL**: `https://<server>/remote.php/dav/`

**Authentication**:
- Basic Auth: `Authorization: Basic <base64(username:password)>`
- Bearer Token: `Authorization: Bearer <token>`

#### Testing Requests with cURL

```bash
# Basic PROPFIND request
curl -X PROPFIND \
  -H "Authorization: Basic $(echo -n 'username:password' | base64)" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/files/username/"

# With XML body
curl -X PROPFIND \
  -H "Authorization: Basic $(echo -n 'username:password' | base64)" \
  -H "Content-Type: application/xml" \
  -H "Depth: 1" \
  -d '<?xml version="1.0"?>
<d:searchrequest xmlns:d="DAV:">
  <d:basicsearch>
    <d:select>
      <d:prop>
        <d:getlastmodified/>
        <d:getcontentlength/>
        <d:getcontenttype/>
      </d:prop>
    </d:select>
    <d:from>
      <d:scope>
        <d:href>/files/username/</d:href>
        <d:depth>infinity</d:depth>
      </d:scope>
    </d:from>
  </d:basicsearch>
</d:searchrequest>' \
  "https://cloud.example.com/remote.php/dav/files/username/"
```

#### Listing Folders (RFC 4918)

**Endpoint**: `/remote.php/dav/files/<username>/`

**Request**:
```bash
curl -X PROPFIND \
  -H "Authorization: Basic <credentials>" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/files/<username>/"
```

**Response**: XML with file properties including:
- `d:getlastmodified`
- `d:getcontentlength`
- `d:getcontenttype`
- `d:resourcetype`
- `oc:id` (Nextcloud-specific)
- `oc:fileid`

#### Downloading Files

**Endpoint**: `/remote.php/dav/files/<username>/<path>`

**Request**:
```bash
curl -X GET \
  -H "Authorization: Basic <credentials>" \
  -o "local_file.txt" \
  "https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt"
```

#### Uploading Files

**Endpoint**: `/remote.php/dav/files/<username>/<path>`

**Request**:
```bash
curl -X PUT \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/octet-stream" \
  --data-binary "@local_file.txt" \
  "https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt"
```

**Response**:
- `201 Created`: File created
- `204 No Content`: File updated

#### Creating Folders (RFC 4918)

**Endpoint**: `/remote.php/dav/files/<username>/<folder_path>/`

**Request**:
```bash
curl -X MKCOL \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/remote.php/dav/files/<username>/new_folder/"
```

**Response**: `201 Created`

#### Deleting Files and Folders (RFC 4918)

**Endpoint**: `/remote.php/dav/files/<username>/<path>`

**Request**:
```bash
curl -X DELETE \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt"
```

**Response**: `204 No Content`

#### Moving Files and Folders (RFC 4918)

**Endpoint**: `/remote.php/dav/files/<username>/<source_path>`

**Request**:
```bash
curl -X MOVE \
  -H "Authorization: Basic <credentials>" \
  -H "Destination: https://cloud.example.com/remote.php/dav/files/<username>/new_path/file.txt" \
  -H "Overwrite: T" \
  "https://cloud.example.com/remote.php/dav/files/<username>/old_path/file.txt"
```

**Response**: `201 Created` or `204 No Content`

#### Copying Files and Folders (RFC 4918)

**Request**:
```bash
curl -X COPY \
  -H "Authorization: Basic <credentials>" \
  -H "Destination: https://cloud.example.com/remote.php/dav/files/<username>/copy_path/file.txt" \
  -H "Overwrite: T" \
  "https://cloud.example.com/remote.php/dav/files/<username>/original_path/file.txt"
```

#### Settings Favorites

**Endpoint**: `/remote.php/dav/files/<username>/favorites/`

**Add to Favorites**:
```bash
curl -X PROPPATCH \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0"?>
<d:propertyupdate xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
  <d:set>
    <d:prop>
      <oc:favorite>1</oc:favorite>
    </d:prop>
  </d:set>
</d:propertyupdate>' \
  "https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt"
```

**Remove from Favorites**:
```bash
curl -X PROPPATCH \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0"?>
<d:propertyupdate xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns">
  <d:remove>
    <d:prop>
      <oc:favorite/>
    </d:prop>
  </d:remove>
</d:propertyupdate>' \
  "https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt"
```

**List Favorites**:
```bash
curl -X PROPFIND \
  -H "Authorization: Basic <credentials>" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/files/<username>/favorites/"
```

### Search

#### Making Search Requests

**Endpoint**: `/remote.php/dav/files/<username>/search`

**Request**:
```bash
curl -X SEARCH \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0"?>
<d:searchrequest xmlns:d="DAV:">
  <d:basicsearch>
    <d:select>
      <d:prop>
        <d:getlastmodified/>
        <d:getcontentlength/>
        <d:displayname/>
      </d:prop>
    </d:select>
    <d:from>
      <d:scope>
        <d:href>/files/<username>/</d:href>
        <d:depth>infinity</d:depth>
      </d:scope>
    </d:from>
    <d:where>
      <d:like>
        <d:prop><d:displayname/></d:prop>
        <d:literal>%search_term%</d:literal>
      </d:like>
    </d:where>
  </d:basicsearch>
</d:searchrequest>' \
  "https://cloud.example.com/remote.php/dav/files/<username>/search"
```

#### Supported DAV Properties

Common properties:
- `d:getlastmodified`: Last modification time
- `d:getcontentlength`: File size in bytes
- `d:getcontenttype`: MIME type
- `d:resourcetype`: Resource type
- `d:displayname`: Display name
- `oc:id`: Internal ID
- `oc:fileid`: File ID
- `oc:size`: File size
- `oc:permissions`: Permission string
- `oc:favorite`: Favorite status
- `oc:tags`: File tags
- `oc:share-types`: Share types

#### Search Examples

**Search by filename**:
```xml
<d:where>
  <d:like>
    <d:prop><d:displayname/></d:prop>
    <d:literal>%document%</d:literal>
  </d:like>
</d:where>
```

**Search by file type**:
```xml
<d:where>
  <d:eq>
    <d:prop><d:getcontenttype/></d:prop>
    <d:literal>image/jpeg</d:literal>
  </d:eq>
</d:where>
```

### Trashbin

**Endpoint**: `/remote.php/dav/trashbin/<username>/`

#### Listing Trashbin Content

**Request**:
```bash
curl -X PROPFIND \
  -H "Authorization: Basic <credentials>" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/trashbin/<username>/"
```

**Properties**:
- `oc:trashbin-original-path`: Original file path
- `oc:trashbin-original-filename`: Original filename
- `oc:trashbin-deletion-time`: Deletion timestamp

#### Restoring from Trashbin

**Request**:
```bash
curl -X MOVE \
  -H "Authorization: Basic <credentials>" \
  -H "Destination: https://cloud.example.com/remote.php/dav/files/<username>/restored_path/file.txt" \
  -H "Overwrite: T" \
  "https://cloud.example.com/remote.php/dav/trashbin/<username>/trash_path/file.txt"
```

#### Deleting from Trashbin

**Request**:
```bash
curl -X DELETE \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/remote.php/dav/trashbin/<username>/trash_path/file.txt"
```

#### Emptying Trashbin

**Request**:
```bash
curl -X DELETE \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/remote.php/dav/trashbin/<username>/"
```

### Versions

**Endpoint**: `/remote.php/dav/files/<username>/versions/`

#### Listing Versions of a File

**Request**:
```bash
curl -X PROPFIND \
  -H "Authorization: Basic <credentials>" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/files/<username>/versions/path/to/file.txt"
```

**Properties**:
- `oc:version-label`: Version label/timestamp
- `oc:version-created`: Creation timestamp
- `oc:version-size`: Version size

#### Restoring a Version

**Request**:
```bash
curl -X COPY \
  -H "Authorization: Basic <credentials>" \
  -H "Destination: https://cloud.example.com/remote.php/dav/files/<username>/path/to/file.txt" \
  -H "Overwrite: T" \
  "https://cloud.example.com/remote.php/dav/files/<username>/versions/path/to/file.txt/v2"
```

### Chunked File Upload

**Endpoint**: `/remote.php/dav/uploads/<username>/`

#### Usage

**1. Initiate chunked upload**:
```bash
curl -X POST \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/xml" \
  -H "X-OC-Chunked-Upload: 1" \
  -d '<?xml version="1.0"?>
<oc:chunked-upload xmlns:oc="http://owncloud.org/ns">
  <oc:file-name>large_file.bin</oc:file-name>
  <oc:file-size>104857600</oc:file-size>
  <oc:chunk-size>10485760</oc:chunk-size>
</oc:chunked-upload>' \
  "https://cloud.example.com/remote.php/dav/uploads/<username>/"
```

**2. Upload chunks**:
```bash
curl -X PUT \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/octet-stream" \
  -H "X-OC-Chunked-Upload: 1" \
  -H "X-OC-Chunked-Upload-Id: <upload_id>" \
  -H "X-OC-Chunked-Upload-Index: 0" \
  -H "Content-Length: 10485760" \
  --data-binary "@chunk0.bin" \
  "https://cloud.example.com/remote.php/dav/uploads/<username>/<upload_id>/0"
```

**3. Complete upload**:
```bash
curl -X POST \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/xml" \
  -H "X-OC-Chunked-Upload: 1" \
  -H "X-OC-Chunked-Upload-Id: <upload_id>" \
  -H "X-OC-Chunked-Upload-Finalize: 1" \
  -d '<?xml version="1.0"?>
<oc:chunked-upload xmlns:oc="http://owncloud.org/ns">
  <oc:destination>/files/<username>/large_file.bin</oc:destination>
</oc:chunked-upload>' \
  "https://cloud.example.com/remote.php/dav/uploads/<username>/<upload_id>"
```

### File Bulk Upload

**Endpoint**: `/remote.php/dav/bulk/<username>/`

**Request**:
```bash
curl -X POST \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: multipart/form-data" \
  -F "file1=@local_file1.txt" \
  -F "file2=@local_file2.txt" \
  -F "destination=/files/<username>/bulk_upload/" \
  "https://cloud.example.com/remote.php/dav/bulk/<username>/"
```

### Comments

**Endpoint**: `/remote.php/dav/files/<username>/comments/`

**Create comment**:
```bash
curl -X POST \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/json" \
  -d '{"message": "This is a comment", "referenceId": "fileid123"}' \
  "https://cloud.example.com/remote.php/dav/files/<username>/comments/<fileid>"
```

---

## OCS API

Open Collaboration Services (OCS) API is Nextcloud's REST-like API for server-specific functionality.

### OCS APIs Overview

**Base URL**: `https://<server>/ocs/v1.php/` or `https://<server>/ocs/v2.php/`

**Request Headers**:
- `OCS-APIRequest: true` - Required for all OCS requests
- `Authorization: Basic <credentials>` or `Authorization: Bearer <token>`

**Response Format**: XML by default, JSON with `Accept: application/json`

**Response Structure**:
```xml
<ocs>
  <meta>
    <status>ok|error</status>
    <statuscode>200|401|403|404|...</statuscode>
    <message>Human readable message</message>
  </meta>
  <data>
    <!-- API-specific data -->
  </data>
</ocs>
```

### Testing Requests with cURL

**Basic OCS Request**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/files/api/v1/file"
```

**JSON Response**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -H "Accept: application/json" \
  "https://cloud.example.com/ocs/v2.php/apps/files/api/v1/file"
```

### User Metadata

**Endpoint**: `/ocs/v1.php/cloud/user`

**Get User Metadata**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/cloud/user"
```

**Response**:
```xml
<ocs>
  <meta>
    <status>ok</status>
    <statuscode>200</statuscode>
  </meta>
  <data>
    <id>user123</id>
    <display-name>John Doe</display-name>
    <email>john@example.com</email>
    <quota>
      <free>1073741824</free>
      <used>536870912</used>
      <total>1610612736</total>
      <relative>0.333</relative>
    </quota>
  </data>
</ocs>
```

**List User IDs**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/cloud/users"
```

### Capabilities API

**Endpoint**: `/ocs/v1.php/cloud/capabilities`

**Request**:
```bash
curl -H "OCS-APIRequest: true" \
  "https://cloud.example.com/ocs/v1.php/cloud/capabilities"
```

**Response**: Server capabilities including files, sharing, version, theming info

### Theming Capabilities

**Endpoint**: `/ocs/v2.php/cloud/capabilities`

Includes theming information:
```xml
<capabilities>
  <theming>
    <name>Nextcloud</name>
    <url>https://nextcloud.com</url>
    <slogan>a safe home for all your data</slogan>
    <color>#0082c9</color>
    <color-text>#ffffff</color-text>
    <logo>https://cloud.example.com/core/img/logo/logo.png</logo>
  </theming>
</capabilities>
```

### Direct Download

**Endpoint**: `/ocs/v1.php/cloud/download`

**Request**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/cloud/download?path=/path/to/file.txt&filesize=1024"
```

### Notifications

**Endpoint**: `/ocs/v2.php/apps/notifications/api/v2/notifications`

**List Notifications**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/notifications/api/v2/notifications"
```

**Get Specific Notification**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/notifications/api/v2/notifications/<notification_id>"
```

**Delete Notification**:
```bash
curl -X DELETE \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/notifications/api/v2/notifications/<notification_id>"
```

### Auto-complete and User Search

**Endpoint**: `/ocs/v1.php/cloud/users/search`

**Request**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -d "search=<search_term>&limit=10" \
  "https://cloud.example.com/ocs/v1.php/cloud/users/search"
```

### OCS Share API

**Endpoint**: `/ocs/v1.php/apps/files_sharing/api/v1/`

#### Local Shares

**Get All Shares**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares"
```

**Parameters**:
- `path`: Filter by file path
- `reshares`: Include reshares (true/false)
- `subfiles`: Include subfiles (true/false)

**Get Shares from Specific File**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares?path=/path/to/file.txt"
```

**Get Share Information**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares/<share_id>"
```

**Create New Share**:
```bash
curl -X POST \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/json" \
  -d '{
    "path": "/path/to/file.txt",
    "shareType": 0,
    "shareWith": "username",
    "permissions": 19,
    "name": "Custom share name",
    "password": "share_password",
    "expireDate": "2024-12-31"
  }' \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares"
```

**Share Types**:
- `0`: User
- `1`: Group
- `3`: Public link
- `6`: Federated Cloud Share

**Permissions**:
- `1`: Read
- `2`: Update
- `4`: Create
- `8`: Delete
- `16`: Share
- `31`: All permissions

**Delete Share**:
```bash
curl -X DELETE \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares/<share_id>"
```

**Update Share**:
```bash
curl -X PUT \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/json" \
  -d '{
    "permissions": 15,
    "password": "new_password",
    "expireDate": "2025-12-31"
  }' \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares/<share_id>"
```

#### Federated Cloud Shares

**Create Federated Cloud Share**:
```bash
curl -X POST \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/json" \
  -d '{
    "path": "/path/to/file.txt",
    "shareType": 6,
    "shareWith": "user@remote-server.com",
    "permissions": 19
  }' \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares"
```

**List Accepted Federated Shares**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/federated_shares"
```

**Delete Federated Share**:
```bash
curl -X DELETE \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/federated_shares/<share_id>"
```

**List Pending Federated Shares**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/pending_federated_shares"
```

**Accept Pending Federated Share**:
```bash
curl -X POST \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -d "accept=1" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/pending_federated_shares/<share_id>"
```

**Decline Pending Federated Share**:
```bash
curl -X DELETE \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/pending_federated_shares/<share_id>"
```

### OCS Sharee API

**Endpoint**: `/ocs/v1.php/apps/files_sharing/api/v1/sharees`

**Search Sharees**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -d "search=<search_term>&itemType=file&format=json" \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/sharees"
```

**Parameters**:
- `search`: Search term
- `itemType`: `file`, `folder`, or `both`
- `format`: `json` or `xml`
- `perPage`: Results per page

**Sharee Recommendations**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -d "itemType=file&format=json" \
  "https://cloud.example.com/ocs/v2.php/apps/files_sharing/api/v1/sharees/recommendations"
```

### OCS Status API

**Endpoint**: `/ocs/v2.php/apps/user_status/api/v1/`

**Fetch Your Own Status**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/user_status"
```

**Set Your Own Status**:
```bash
curl -X PUT \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  -H "Content-Type: application/json" \
  -d '{
    "statusType": "custom",
    "message": "In a meeting",
    "statusIcon": "📅",
    "clearAt": "2024-01-01T14:00:00Z"
  }' \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/user_status"
```

**Status Types**: `online`, `away`, `dnd`, `invisible`, `custom`

**Clear Message**:
```bash
curl -X DELETE \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/user_status"
```

**Fetch Predefined Statuses**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/predefined_statuses"
```

**Fetch All User Statuses**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/user_statuses"
```

**Fetch Specific User Status**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/user_status/api/v1/user_statuses/<userId>"
```

---

## Login Flow

The Login Flow provides a secure way for clients to obtain login credentials without storing user passwords.

### Opening the Webview

**Endpoint**: `<server>/index.php/login/flow`

**Requirements**:
- Set `OCS-APIREQUEST` header to `true`
- Register URL handler for `nc://` protocol
- No cookies should be set
- No passwords should be stored
- No state should be preserved

**Request**:
```bash
# Open in webview/browser
https://cloud.example.com/index.php/login/flow
```

### Login in the User

The user will:
1. See webpage asking to grant access to client (identified by USER_AGENT)
2. Follow login steps (including 2FA if enabled)
3. Client doesn't handle authentication details

### Obtaining the Login Credentials

After successful login, server redirects to:
```
nc://login/server:<server>&user:<loginname>&password:<password>
```

**Parameters**:
- `server`: Server address (may include protocol, defaults to https)
- `loginname`: Username for login
- `password`: App password for authentication

**Important**: Fetch actual username from OCS API: `<server>/ocs/v1.php/cloud/user`

### Converting to App Passwords

**Endpoint**: `/ocs/v2.php/core/getapppassword`

**Request**:
```bash
curl -u username:password \
  -H "OCS-APIRequest: true" \
  "https://cloud.example.com/ocs/v2.php/core/getapppassword"
```

**Response**:
```xml
<?xml version="1.0"?>
<ocs>
  <meta>
    <status>ok</status>
    <statuscode>200</statuscode>
    <message>OK</message>
  </meta>
  <data>
    <apppassword>M1DqHwuZWwjEC3ku7gJsspR7bZXopwf01kj0XGppYVzEkGtbZBRaXlOUxFZdbgJ6Zk9OwG9x</apppassword>
  </data>
</ocs>
```

**Behavior**:
- If already using app password: Returns 403
- If using real password: Generates and returns new app password

### Deleting an App Password

**Endpoint**: `/ocs/v2.php/core/apppassword`

**Request**:
```bash
curl -u username:app-password \
  -X DELETE \
  -H "OCS-APIREQUEST: true" \
  "https://cloud.example.com/ocs/v2.php/core/apppassword"
```

**Response**:
```xml
<?xml version="1.0"?>
<ocs>
  <meta>
    <status>ok</status>
    <statuscode>200</statuscode>
    <message>OK</message>
  </meta>
  <data/>
</ocs>
```

**Error Handling**: If non-200 status, still proceed with removing the account

### Login Flow v2

Alternative flow using default web browser for authentication.

**Initiate Login**:
```bash
curl -X POST \
  "https://cloud.example.com/index.php/login/v2"
```

**Response**:
```json
{
  "poll": {
    "token": "mQUYQdffOSAMJYtm8pVpkOsVqXt5hglnuSpO5EMbgJMNEPFGaiDe8OUjvrJ2WcYcBSLgqynu9jaPFvZHMl83ybMvp6aDIDARjTFIBpRWod6p32fL9LIpIStvc6k8Wrs1",
    "endpoint": "https://cloud.example.com/login/v2/poll"
  },
  "login": "https://cloud.example.com/login/v2/flow/guyjGtcKPTKCi4epIRIupIexgJ8wNInMFSfHabACRPZUkmEaWZSM54bFkFuzWksbps7jmTFQjeskLpyJXyhpHlgK8sZBn9HXLXjohIx5iXgJKdOkkZTYCzUWHlsg3YFg"
}
```

**Process**:
1. Open `login` URL in default browser
2. User completes login procedure
3. Poll the `poll.endpoint`

**Poll for Completion**:
```bash
curl -X POST \
  -d "token=mQUYQdffOSAMJYtm8pVpkOsVqXt5hglnuSpO5EMbgJMNEPFGaiDe8OUjvrJ2WcYcBSLgqynu9jaPFvZHMl83ybMvp6aDIDARjTFIBpRWod6p32fL9LIpIStvc6k8Wrs1" \
  "https://cloud.example.com/login/v2/poll"
```

**Response** (on completion):
```json
{
  "server": "https://cloud.example.com",
  "loginName": "username",
  "appPassword": "yKTVA4zgxjfivy52WqD8kW3M2pKGQr6srmUXMipRdunxjPFripJn0GMfmtNOqOolYSuJ6sCN"
}
```

### Troubleshooting

**Login Name vs. Email Login**:
- Nextcloud allows authentication with login name (UID, email, etc.)
- The identifier used in web session must match the identifier used in connecting client
- Use OCS API to get actual username

---

## Remote Wipe

Remote wipe functionality allows users to wipe lost or stolen devices.

**Prerequisite**: Clients must use the login flow to have dedicated tokens.

### Obtaining Wipe Status

**Endpoint**: `<server>/index.php/core/wipe/check`

**Request**:
```bash
curl -X POST \
  -d "token=<TOKEN>" \
  "https://cloud.example.com/index.php/core/wipe/check"
```

**Response** (wipe required):
```json
{
  "wipe": true
}
```

**Action**: If `wipe: true`, proceed to wipe the device

### Wiping the Actual Device

**Requirements**: Remove all user data linked to the account:
- Caches
- Offline files
- The actual account itself

### Signalling Completion

**Endpoint**: `<server>/index.php/core/wipe/success`

**Request**:
```bash
curl -X POST \
  -d "token=<TOKEN>" \
  "https://cloud.example.com/index.php/core/wipe/success"
```

**Purpose**: Signals server that wipe is complete and triggers final cleanup

---

## Activity API

The Activity API provides access to user activity feeds and notifications.

### Endpoints

**Base Endpoint**: `/ocs/v2.php/apps/activity/api/v2/`

### Common Operations

**Get Activity Feed**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/activity/api/v2/activity"
```

**Parameters**:
- `since`: Only activities after this timestamp
- `limit`: Maximum number of activities to return
- `object_type`: Filter by object type
- `object_id`: Filter by specific object ID

**Get Specific Activity**:
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic <credentials>" \
  "https://cloud.example.com/ocs/v2.php/apps/activity/api/v2/activity/<activity_id>"
```

**Activity Types**:
- `file_created`: File created
- `file_changed`: File modified
- `file_deleted`: File deleted
- `file_restored`: File restored
- `share_created`: Share created
- `share_accepted`: Share accepted
- `share_declined`: Share declined

---

## Best Practices

### Authentication
1. **Use App Passwords**: Always use app passwords instead of user passwords
2. **Secure Storage**: Store credentials securely using platform-specific secure storage
3. **Token Rotation**: Implement token rotation for long-running applications
4. **Scope Limitation**: Request only necessary permissions

### Error Handling
1. **HTTP Status Codes**: Handle all HTTP status codes appropriately
2. **Rate Limiting**: Implement retry logic with exponential backoff
3. **Network Errors**: Handle network connectivity issues gracefully
4. **API Versioning**: Use versioned API endpoints for compatibility

### Performance
1. **Batching**: Use bulk operations when possible
2. **Caching**: Cache responses appropriately
3. **Pagination**: Handle pagination for large result sets
4. **Compression**: Use compression for large file transfers

### Security
1. **HTTPS**: Always use HTTPS for connections
2. **Input Validation**: Validate all user inputs
3. **Output Encoding**: Properly encode output to prevent XSS
4. **CSRF Protection**: Implement CSRF protection for web applications
5. **Certificate Validation**: Validate SSL certificates

---

## Code Examples

### PHP Example - WebDAV File Upload

```php
<?php
function uploadToNextcloud($server, $username, $password, $localPath, $remotePath) {
    $url = "$server/remote.php/dav/files/$username/$remotePath";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_INFILE, fopen($localPath, 'r'));
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localPath));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode >= 200 && $httpCode < 300;
}

// Usage
$success = uploadToNextcloud(
    'https://cloud.example.com',
    'username',
    'app-password',
    '/local/path/file.txt',
    'remote/path/file.txt'
);
```

### JavaScript Example - OCS API Request

```javascript
async function getUserInfo(server, username, password) {
    const url = `${server}/ocs/v1.php/cloud/user`;
    
    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'OCS-APIRequest': 'true',
            'Authorization': 'Basic ' + btoa(`${username}:${password}`)
        }
    });
    
    const data = await response.json();
    return data.ocs.data;
}

// Usage
getUserInfo('https://cloud.example.com', 'username', 'app-password')
    .then(userInfo => console.log(userInfo))
    .catch(error => console.error(error));
```

### Python Example - Login Flow v2

```python
import requests
import webbrowser
import time

def login_flow_v2(server):
    # Initiate login
    response = requests.post(f"{server}/index.php/login/v2")
    data = response.json()
    
    # Open browser for user login
    webbrowser.open(data['login'])
    
    # Poll for completion
    token = data['poll']['token']
    poll_url = data['poll']['endpoint']
    
    while True:
        time.sleep(2)  # Poll every 2 seconds
        poll_response = requests.post(poll_url, data={'token': token})
        
        if poll_response.status_code == 200:
            result = poll_response.json()
            return {
                'server': result['server'],
                'loginName': result['loginName'],
                'appPassword': result['appPassword']
            }
        elif poll_response.status_code != 404:
            raise Exception("Login failed")
```

---

## Resources

- [Nextcloud Developer Documentation](https://docs.nextcloud.com/server/24/developer_manual/)
- [Nextcloud API Reference](https://docs.nextcloud.com/server/24/developer_manual/client_apis/)
- [WebDAV RFC 4918](https://tools.ietf.org/html/rfc4918)
- [OCS API Specification](https://github.com/owncloud/opencloudmesh)

---
*This skill is based on Nextcloud Server 24 documentation and provides comprehensive coverage of the Client APIs for integration and development purposes.*
