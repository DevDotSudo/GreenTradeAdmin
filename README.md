# Green Trade Admin Panel

A comprehensive admin panel for the Green Trade platform built with PHP and Firebase authentication. This admin panel allows administrators to manage sellers, products, and orders with a modern, responsive design.

## Features

### 🔐 Authentication System
- **Firebase Authentication**: Secure login and registration using Firebase
- **Email Verification**: Automatic email verification for new accounts
- **Password Reset**: Email-based password recovery system
- **Session Management**: Secure session handling with PHP

### 📊 Dashboard
- **Overview Statistics**: Total sellers, products, pending orders, and sales
- **Recent Orders**: Latest order activity with status tracking
- **Quick Actions**: Direct access to manage sellers, products, and orders
- **Responsive Design**: Works seamlessly on desktop and mobile devices

### 👥 Seller Management
- **Seller Listing**: View all registered sellers with detailed information
- **Search & Filter**: Find sellers by name, email, location, or status
- **Status Management**: Active, inactive, and pending status tracking
- **Seller Actions**: View, edit, and delete seller accounts
- **Performance Metrics**: Track seller performance and sales data

### 🛍️ Product Management
- **Product Catalog**: Complete product listing with categories
- **Inventory Tracking**: Stock levels and availability status
- **Category Filtering**: Filter products by category (Vegetables, Fruits, Grains, Dairy, Meat)
- **Product Actions**: View, edit, and delete products
- **Stock Alerts**: Low stock and out-of-stock indicators

### 📦 Order Management
- **Order Tracking**: Complete order lifecycle management
- **Status Updates**: Pending, Processing, Delivered, Cancelled statuses
- **Customer Information**: Detailed customer and seller information
- **Date Filtering**: Filter orders by today, this week, or this month
- **Export Functionality**: Export order data (placeholder for implementation)

### 🎨 Design Features
- **Green Trade Branding**: Consistent with the main platform design
- **Color Palette**: 
  - Dark Green (#1E4620) - Headers and navigation
  - Light Green (#E8F5E9) - Background
  - Medium Green (#2E7D32) - Buttons and accents
  - White - Content areas
- **Modern UI**: Clean, professional interface with smooth animations
- **Responsive Layout**: Mobile-friendly design
- **Confirmation Modals**: User-friendly confirmation dialogs

## File Structure

```
GreenTradeAdmin/
├── index.php              # Login page
├── register.php           # Admin registration
├── forgot-password.php    # Password recovery
├── dashboard.php          # Main admin dashboard
├── manage-sellers.php     # Seller management
├── manage-products.php    # Product management
├── orders.php            # Order management
└── README.md             # Documentation
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Firebase project with Authentication enabled

### Installation

1. **Clone or download** the project files to your web server directory

2. **Configure Firebase**:
   - Create a Firebase project at [Firebase Console](https://console.firebase.google.com/)
   - Enable Authentication with Email/Password provider
   - Copy your Firebase configuration (already included in the code)

3. **Set up Email Templates** (Optional):
   - Configure Firebase Authentication email templates
   - Customize password reset and verification emails

4. **Access the Admin Panel**:
   - Navigate to `index.php` in your browser
   - Register a new admin account
   - Login with your credentials

## Usage Guide

### Admin Registration
1. Visit the registration page
2. Fill in your details (First Name, Last Name, Email, Password)
3. Meet password requirements (8+ chars, uppercase, lowercase, number, special char)
4. Verify your email address
5. Login to access the admin panel

### Managing Sellers
- View all registered sellers in the seller list
- Use search to find specific sellers
- Filter by status (Active, Inactive, Pending) or location
- Perform actions: View details, Edit information, Delete accounts

### Managing Products
- Browse the complete product catalog
- Search products by name, category, or seller
- Filter by category or stock status
- Monitor inventory levels and stock alerts
- Add, edit, or remove products

### Managing Orders
- Track all platform orders
- Filter by status or date range
- View detailed customer and seller information
- Update order statuses
- Export order data for reporting

## Security Features

- **Firebase Authentication**: Industry-standard authentication
- **Session Management**: Secure PHP session handling
- **Input Validation**: Client and server-side validation
- **Password Requirements**: Strong password policies
- **Email Verification**: Account verification system
- **Secure Logout**: Proper session termination

## Customization

### Colors and Branding
The color scheme can be easily modified by updating the CSS variables in each file:

```css
/* Primary Colors */
--dark-green: #1E4620;
--light-green: #E8F5E9;
--medium-green: #2E7D32;
--accent-green: #34C759;
```

### Adding New Features
The modular structure makes it easy to add new functionality:
- Create new PHP files for additional pages
- Follow the existing design patterns
- Include Firebase authentication checks
- Maintain consistent navigation structure

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers (iOS Safari, Chrome Mobile)

## Future Enhancements

- **Database Integration**: Connect to a real database for data persistence
- **Real-time Updates**: Implement real-time data synchronization
- **Advanced Analytics**: Add detailed reporting and analytics
- **Bulk Operations**: Enable bulk editing and management
- **API Integration**: Connect with external services
- **Multi-language Support**: Internationalization features
- **Advanced Search**: Full-text search capabilities
- **Notification System**: Real-time notifications for admins

## Support

For technical support or questions about the Green Trade Admin Panel, please contact:
- Email: info@greentrade.com
- Phone: +123 456 7890

## License

This project is developed for the Green Trade platform. All rights reserved.

---

**Green Trade Admin Panel** - Connecting farmers and buyers for fresh agricultural products.
"# GreenTradeAdmin" 
