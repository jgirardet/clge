# Nextcloud API Skill

This skill provides comprehensive documentation for Nextcloud Client APIs based on Nextcloud Server 24.

## Features

- **Complete WebDAV API documentation** with all operations (file management, search, trashbin, versions, chunked upload, bulk upload, comments)
- **Full OCS API reference** including user management, sharing, notifications, and status APIs
- **Login Flow v1 and v2** documentation with practical examples
- **Remote Wipe** functionality for device management
- **Activity API** for user activity tracking
- **Practical code examples** in PHP, JavaScript, and Python
- **Best practices** and security guidelines
- **cURL examples** for every API operation

## Structure

```
skills/nextcloud-api/
├── SKILL.md          # Main skill documentation
└── README.md         # This file
```

## Usage

Reference this skill when:
- Working with Nextcloud integrations
- Implementing Nextcloud client applications
- Debugging Nextcloud API issues
- Needing API examples or endpoint references
- Developing file synchronization tools
- Building sharing functionality
- Implementing authentication flows

## API Coverage

### WebDAV API
- Basic operations (PROPFIND, GET, PUT, DELETE, MKCOL, COPY, MOVE)
- File and folder management
- Favorites management
- Advanced search
- Trashbin operations
- File versioning
- Chunked file upload
- Bulk file upload
- Comments

### OCS API
- User metadata and management
- Server capabilities
- Theming information
- Direct download
- Notifications
- User search and autocomplete
- Share API (local and federated)
- Sharee API
- User Status API

### Authentication
- Login Flow v1 (webview-based)
- Login Flow v2 (browser-based)
- App password management
- Token-based authentication

### Device Management
- Remote wipe functionality
- Wipe status checking
- Completion signaling

### Activity
- Activity feed retrieval
- Activity filtering
- Specific activity access

## Quick Reference

### Base URLs
```
WebDAV: https://<server>/remote.php/dav/
OCS v1: https://<server>/ocs/v1.php/
OCS v2: https://<server>/ocs/v2.php/
```

### Common Headers
```
Authorization: Basic <base64(username:password)>
OCS-APIRequest: true
Accept: application/json  # For JSON responses
```

## Examples

### List files via WebDAV
```bash
curl -X PROPFIND \
  -H "Authorization: Basic $(echo -n 'user:pass' | base64)" \
  -H "Depth: 1" \
  "https://cloud.example.com/remote.php/dav/files/user/"
```

### Get user info via OCS
```bash
curl -H "OCS-APIRequest: true" \
  -H "Authorization: Basic $(echo -n 'user:pass' | base64)" \
  "https://cloud.example.com/ocs/v1.php/cloud/user"
```

### Create share
```bash
curl -X POST \
  -H "OCS-APIRequest: true" \
  -H "Authorization: Basic $(echo -n 'user:pass' | base64)" \
  -H "Content-Type: application/json" \
  -d '{"path": "/file.txt", "shareType": 0, "shareWith": "otheruser", "permissions": 19}' \
  "https://cloud.example.com/ocs/v1.php/apps/files_sharing/api/v1/shares"
```

## Version

Based on **Nextcloud Server 24** documentation.

## License

This skill documentation is derived from the official Nextcloud documentation, which is licensed under the [AGPL-3.0](https://www.gnu.org/licenses/agpl-3.0.html) license.

## Contributing

To update this skill:
1. Check the latest Nextcloud documentation
2. Update the SKILL.md file with new endpoints and features
3. Add new code examples as needed
4. Test all examples against a live Nextcloud instance

## Support

For issues with this skill or to request additional API coverage, please refer to the main project documentation or create an issue in the repository.