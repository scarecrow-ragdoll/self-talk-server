# SelfTalk - Project Roadmap

> Self-hosted messaging platform with end-to-end encryption and full data ownership

## Project Overview

**SelfTalk** is a self-hosted messaging server built with Laravel that allows users to deploy their own private messenger instance with real-time communication, end-to-end encryption, and complete control over their data.

### Tech Stack
- **Backend:** Laravel 12 + Laravel Reverb (WebSocket) + PostgreSQL + Redis
- **Client:** Flutter (Web + Android + iOS)
- **Infrastructure:** Docker + Docker Compose

---

## MVP - Minimum Viable Product (2-4 weeks)

### Backend (Laravel 12)

**Core Infrastructure:**
- Laravel 12 project setup with Docker
- PostgreSQL database configuration
- Redis for caching and queues
- Laravel Reverb for WebSocket connections
- Laravel Sanctum for API authentication

**Database Models:**
- `User` - user accounts
- `Room` - chat rooms (direct and group)
- `Message` - chat messages
- `RoomUser` - pivot table for room members

**API Endpoints:**
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/rooms
POST   /api/rooms
GET    /api/rooms/{id}/messages
POST   /api/rooms/{id}/messages
GET    /api/users
```

**WebSocket Channels:**
- `presence-room.{roomId}` - for messages and user presence

**Features:**
- User registration and authentication (JWT via Sanctum)
- Create and list chat rooms
- Send and receive text messages in real-time
- Message history storage
- Basic user profile

### Client (Flutter)

**Screens:**
- Server connection screen (enter server URL)
- Registration/Login
- Room list
- Chat interface
- User settings

**Features:**
- Configure server URL (save to secure storage)
- User authentication
- Display list of available rooms
- Real-time message sending/receiving via WebSocket
- Message history with pagination
- Simple Material Design UI
- Local message caching

**Packages:**
- `web_socket_channel` - WebSocket connectivity
- `dio` - HTTP requests
- `flutter_secure_storage` - secure token storage
- `provider` or `riverpod` - state management

---

## Phase 1 - Security & Core Features (1-2 months)

### Backend

**Security:**
- Rate limiting for API endpoints
- Input validation and sanitization
- CSRF protection
- SQL injection prevention
- XSS protection

**Performance:**
- Database indexes for queries
- Query optimization
- Redis caching strategy
- Database connection pooling

**Features:**
- Online/offline user status (Presence channels)
- Typing indicators
- Message read receipts
- Soft delete for messages
- User blocking functionality
- Last seen timestamp

**DevOps:**
- Laravel Horizon for queue monitoring
- Laravel Telescope for debugging
- Logging configuration
- Error tracking setup

### Client

**UI/UX Improvements:**
- Typing indicator display
- Online status badges
- Read receipt checkmarks
- Pull-to-refresh for message history
- Infinite scroll pagination
- Message timestamps
- Unread message counter

**Performance:**
- Local SQLite database for message caching
- Image caching
- Optimistic UI updates
- Background sync

**Features:**
- Dark/Light theme toggle
- Push notifications (Firebase Cloud Messaging)
- In-app notifications
- Sound notifications toggle
- Custom notification sounds

---

## Phase 2 - Rich Media (1-2 months)

### Backend

**File Upload System:**
- Laravel Storage configuration
- Image upload and storage
- Video upload handling
- Audio file support
- Document upload
- File size limits and validation
- Supported file type restrictions

**Media Processing:**
- Image optimization (intervention/image)
- Thumbnail generation
- Video compression (queue job)
- Audio waveform generation
- MIME type validation
- Virus scanning integration

**Storage:**
- Local storage configuration
- S3/DigitalOcean Spaces support
- CDN integration
- Storage quota per user
- Automatic cleanup of old files

### Client

**Media Features:**
- Image picker (camera/gallery)
- Image viewer with zoom/pan
- Video player
- Audio player with waveform
- Voice message recorder
- File attachment picker
- Download manager
- Media gallery view

**UI Components:**
- Image grid for multiple photos
- Video thumbnail preview
- Audio playback controls
- File download progress
- Media compression before upload

---

## Phase 3 - Advanced Messaging (1 month)

### Backend

**Message Features:**
- Message reactions (polymorphic relation)
- Reply/Quote functionality
- Message editing (with edit history)
- Message deletion (for everyone/for me)
- Forward messages
- Message pinning
- Full-text search (Laravel Scout + PostgreSQL)

**Database:**
- `Reaction` model
- `MessageEdit` model for edit history
- Search indexing
- Message metadata storage

### Client

**Interaction Patterns:**
- Swipe gesture for reply
- Long-press context menu
- Double-tap for quick reaction
- Reaction picker UI
- Edit message dialog
- Forward message selector
- Search interface with filters

**Features:**
- Message threading (reply chains)
- Quoted message preview
- Edit history viewer
- Reaction summary
- Search with highlighting
- Jump to quoted message

---

## Phase 4 - Groups & Permissions (1-2 months)

### Backend

**Room Types:**
- Direct messages (1-to-1)
- Group chats (multiple members)
- Channels (broadcast mode)
- Private vs Public rooms

**Permissions System:**
- Integration with Spatie Laravel Permission
- Roles: Owner, Admin, Moderator, Member
- Permissions: send messages, add members, delete messages, manage settings
- Custom role creation

**Group Management:**
- Create/delete groups
- Add/remove members
- Leave group
- Transfer ownership
- Group settings
- Member list with roles

**Invite System:**
- Generate invite links (signed URLs)
- Invite expiration
- Usage limit per link
- Join requests for private groups
- Approve/deny join requests

**Moderation:**
- Kick members
- Ban users (temporary/permanent)
- Mute users
- Delete messages
- Report system
- Audit logs

### Client

**Group Features:**
- Group creation wizard
- Member management screen
- Role assignment UI
- Invite link generation/sharing
- Join request handling
- Group settings panel

**Admin Panel:**
- Member list with roles
- Moderation actions
- Audit log viewer
- Group statistics
- Banned users list

---

## Phase 5 - End-to-End Encryption (2-3 months)

### Backend

**Encryption Infrastructure:**
- Public key storage per device
- Device verification system
- Key exchange API endpoints
- Encrypted message storage (store encrypted blobs)
- Key backup mechanism
- Device management

**API Endpoints:**
```
POST   /api/keys/upload
GET    /api/keys/{userId}
POST   /api/keys/verify
GET    /api/devices
DELETE /api/devices/{id}
```

**Features:**
- Device fingerprint verification
- Key rotation support
- Encrypted message metadata
- Encrypted attachments
- Safety numbers for verification

### Client

**Encryption:**
- Signal Protocol implementation (libsignal_protocol_dart)
- Double Ratchet algorithm
- X3DH key exchange
- Key generation and storage
- Device key management

**Security UI:**
- Device verification flow
- Safety number display and verification
- Encryption status indicator
- Warning for unverified chats
- Device management screen
- Key backup/restore wizard

**Features:**
- Encrypted local storage
- Secure key storage
- Identity key management
- Session management
- Forward secrecy

---

## Phase 6 - Voice & Video Calls (2-3 months)

### Backend

**WebRTC Signaling:**
- Signaling server via Reverb
- STUN/TURN server configuration
- Call state management
- Call history storage

**Infrastructure:**
- Coturn server setup for TURN
- ICE candidate exchange
- SDP offer/answer handling
- Call events broadcasting

**API:**
```
POST   /api/calls/initiate
POST   /api/calls/{id}/answer
POST   /api/calls/{id}/reject
POST   /api/calls/{id}/end
GET    /api/calls/history
```

### Client

**WebRTC Integration:**
- `flutter_webrtc` package
- Peer connection management
- Media stream handling
- Audio/video device selection

**Call Features:**
- Outgoing call screen
- Incoming call notification
- In-call UI with controls
- Mute/unmute audio
- Enable/disable video
- Switch camera
- Speaker/earpiece toggle
- Call timer
- Screen sharing (optional)

**UI:**
- Full-screen call interface
- Picture-in-picture mode
- Call notifications
- Call history list
- Missed call indicators

---

## Phase 7 - Federation (3+ months)

### Backend

**Federation Protocol:**
- Custom protocol or ActivityPub implementation
- Server-to-server API
- Server discovery mechanism
- Trust and verification system

**Features:**
- Remote server registry
- Cross-server user lookup
- Federated message delivery
- Remote room participation
- Identity verification across servers
- Cross-server encryption

**API:**
```
POST   /api/federation/servers
GET    /api/federation/servers/{domain}
POST   /api/federation/messages
GET    /api/federation/users/{domain}/{username}
```

**Database:**
- `FederatedServer` model
- `RemoteUser` model
- Server trust levels
- Federation logs

### Client

**Federation UI:**
- Server list display
- Remote user profiles
- Cross-server chat indicators
- Federation status badges
- Server discovery interface

**Features:**
- Add federated servers
- Browse remote users/rooms
- Cross-server messaging
- Federation trust warnings
- Handle server disconnections

---

## Phase 8 - Bots & Extensions (2-3 months)

### Backend

**Bot Framework:**
- Bot registration system
- Bot API endpoints
- Webhook support
- Bot permissions
- Rate limiting for bots

**API for Bots:**
```
POST   /api/bots
GET    /api/bots
POST   /api/bots/{id}/webhook
GET    /api/messages (bot access)
POST   /api/messages (bot sending)
```

**Features:**
- Bot authentication tokens
- Command parsing
- Interactive buttons
- Bot analytics
- Bot marketplace

### Client

**Bot Features:**
- Bot directory
- Add bot to room
- Bot commands autocomplete
- Interactive bot messages
- Bot settings per room

---

## Phase 9 - Advanced Features (2-3 months)

### Backend

**Features:**
- Message scheduling
- Auto-reply/away messages
- Custom stickers
- Polls and surveys
- Location sharing
- Contact sharing
- Live location sharing
- Message translation
- GIF integration
- Custom emoji reactions

**Analytics:**
- User activity tracking
- Message statistics
- Storage usage monitoring
- Performance metrics

### Client

**Features:**
- Sticker packs
- GIF picker
- Poll creation and voting
- Location picker
- Contact picker
- Translation toggle
- Custom theme builder
- Backup/restore settings
- Export chat history

---

## Phase 10 - DevOps & Production Ready (1-2 months)

### Infrastructure

**Deployment:**
- Production-ready Docker setup
- Docker Compose for single-server
- Kubernetes manifests for scaling
- Helm charts
- One-click deployment scripts

**Monitoring:**
- Laravel Horizon for queues
- Laravel Telescope (dev only)
- Prometheus metrics export
- Grafana dashboards
- Sentry error tracking
- Log aggregation (ELK stack)

**Backup:**
- Automated database backups
- File storage backups
- Backup rotation policy
- Restore scripts
- Disaster recovery plan

**Security:**
- SSL/TLS enforcement
- Security headers
- DDoS protection
- Fail2ban integration
- Security scanning
- Penetration testing

**Performance:**
- Redis cluster setup
- Database read replicas
- Load balancing
- CDN for static assets
- Asset optimization
- Query optimization

### Documentation

**User Documentation:**
- Installation guide
- User manual
- Admin guide
- FAQ
- Troubleshooting

**Developer Documentation:**
- API documentation (OpenAPI/Swagger)
- Architecture overview
- Database schema
- WebSocket protocol docs
- Bot development guide
- Plugin development guide
- Contributing guidelines

**Deployment:**
- System requirements
- Docker setup guide
- Bare metal installation
- Cloud provider guides (AWS, DigitalOcean, etc.)
- Reverse proxy setup (Nginx/Caddy)
- SSL certificate setup

---

## Phase 11 - Mobile & Desktop Apps (2-3 months)

### Platforms

**Mobile:**
- Android (Google Play)
- iOS (App Store)
- App store optimization
- Push notification setup
- Deep linking
- App updates mechanism

**Desktop:**
- Windows (via Flutter)
- macOS (via Flutter)
- Linux (via Flutter)
- System tray integration
- Desktop notifications
- Auto-start on boot

### Features

**Platform-Specific:**
- OS-native notifications
- Share extension (mobile)
- Widget support
- Picture-in-picture
- Background sync
- Biometric authentication

---

## Phase 12 - Enterprise Features (3+ months)

### Backend

**Enterprise Features:**
- LDAP/Active Directory integration
- SSO (SAML, OAuth)
- Audit logs
- Compliance reports
- Data retention policies
- Legal hold
- E-discovery support

**Administration:**
- Admin dashboard
- User management panel
- Server statistics
- Resource usage monitoring
- License management
- Multi-tenancy support

### Client

**Enterprise UI:**
- Organization profiles
- Department/team organization
- Company directory
- Compliance indicators
- Admin tools in-app

---

## Future Ideas & Experimental

**AI Integration:**
- Message summaries
- Smart replies
- Sentiment analysis
- Language detection
- Spam filtering

**Advanced Features:**
- Disappearing messages
- Screenshot detection
- Self-destructing accounts
- Decoy chat passwords
- Steganography for hiding messages
- Blockchain for message verification

**Social Features:**
- Status updates
- Stories (disappearing posts)
- Public channels discovery
- Hashtags
- @mentions across servers

**Integration:**
- Email gateway
- SMS gateway
- Matrix protocol bridge
- XMPP bridge
- Discord/Slack bridge

---

## Success Metrics

### MVP Success Criteria
- [ ] Successfully deploy server via Docker
- [ ] Register and authenticate users
- [ ] Send/receive real-time messages
- [ ] 10+ beta testers actively using
- [ ] <500ms message delivery latency

### Phase Success Criteria
- [ ] 100+ active users
- [ ] 99.9% uptime
- [ ] <100ms API response time
- [ ] E2E encryption verified by security audit
- [ ] Federation with 5+ other servers
- [ ] 50+ bots created by community

---

## Development Principles

1. **Security First:** Every feature must consider security implications
2. **Privacy by Design:** User data is sacred
3. **Open Source:** Community-driven development
4. **Self-Hostable:** Easy deployment for non-technical users
5. **Federated:** No single point of control
6. **Extensible:** Plugin/bot architecture
7. **Cross-Platform:** Write once, run everywhere
8. **Performance:** Fast and responsive
9. **Accessibility:** Usable by everyone
10. **Documentation:** Well-documented code and features

---

## Contributing

This is a pet project, but contributions are welcome! Check out:
- GitHub Issues for bugs and feature requests
- Pull Requests for code contributions
- Discussions for ideas and questions

---

## License

MIT License - Free to use, modify, and distribute

---

**Last Updated:** February 2026
**Current Phase:** MVP Development
**Next Milestone:** MVP Release (Beta)
