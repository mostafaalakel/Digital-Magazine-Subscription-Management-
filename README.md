# Magazine Management System

A Laravel-based Magazine Management System enabling users to subscribe to magazines, read articles, and comment. Includes role-based access for **Subscribers**, **Publishers**, and **Admins**.

## Features

- **User Roles**:
  - **Subscriber**: Subscribe to magazines, read articles, and comment.
  - **Publisher**: Add magazines and articles.
  - **Admin**: Manage users, subscriptions, and magazines.

- **Subscription Management**:
  - Subscribe for specific durations (monthly/yearly).
  - Track statuses: Active, Expired, Pending.
  - Email notifications for nearing expiration.

- **Payment Integration**:
  - Secure payments with **PayPal**.

- **Commenting System**:
  - Subscribers can comment on articles.
  - Admins can block inappropriate comments.

- **Task Scheduling**:
  - Auto-check expired subscriptions and revoke access.
  - Send subscription/payment reports to admin.

- **Activity Logging**:
  - Logs key actions (e.g., creating magazines, renewing subscriptions).

