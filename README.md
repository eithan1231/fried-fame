# fried-fame
Friend Fame VPN engine.

This README is incomplete, and this VPN is about 90% complete. Refactoring needs to be done for desktop client, as well as ALL the services. They implement a protocol we developed inhouse for RPC, which we would like to migrate to something based on HTTP. We have no active plans for completing this project. If anyone would like to see this project in light, feel free to contact us and we will add it to the TODO list. If you are interested in contributing, feel free to contact me too.

# Feature List

- 2 Factor Authentication
- Administrative Audit Logs (review all Administrative actions)
- Caching system
- Captcha system (easily implement other captchas)
- Email verification and email list
- Error and Exception handling
- Knowledgebase (partial)
- Multi-lingual support (can add your own languages with relative ease)
- Logging system (NOT 'vpn logs', rather a system for logging for DEBUGGING purposes see /library/logger)
- Payment gateway (Currently paypal, easily implement others)
- in-house router (highly efficient)
- Announcements
- Feedback system
- Multiple Currency support
- In addition to payment gateway, it has gift-card support.
- Differnet plans (for payments, kinda like payment 'tiers')
- Rate limiting, help prevention of hacking/cracking accounts by brute-force
- User account system (Registration, Recovery, Login, Sessions, Groups and Permissions)
- Reviews of the VPN (displayed on front-page with censored names)
- Auto-timezone synchronization. (server and client will calculate their time difference, and associate it with the users session. Used globally on the site)
- SMTP-Client (Coded to the SMTP specification, but since migrated to NodeJS for better efficiency)
- Client version management (so we can deploy new versions of loaders/clients for linux/nix/windows etc)
- Automatic database upgrades for when upgrading
- has MUCH more features!

