# Elevator Services Website

A complete elevator services management system that runs entirely in the browser using HTML, CSS, and JavaScript with LocalStorage for data management.

## Features

### Frontend (User Side)
- **Home page** with company introduction and navigation
- **Request Service page** with form for service requests
- **Modules page** displaying different elevator types
- **Contact information** and business hours
- **Responsive design** for all devices

### Backend (Admin Panel)
- **Admin login** with JavaScript-based authentication
- **Dashboard** to view and manage service requests
- **Module management** (Add, Edit, Delete elevator modules)
- **Request status updates** (Pending → In Progress → Completed)
- **LocalStorage-based** data persistence

## Tech Stack

- **HTML5** for structure
- **CSS3** for styling and responsive design
- **JavaScript (ES6+)** for functionality
- **LocalStorage** for data persistence
- **No server required** - runs entirely in browser

## Quick Start

1. Download or clone the project files
2. Open `index.html` in any modern web browser
3. The website is ready to use!

## Pages

### User Pages
- `index.html` - Home page with company information
- `service.html` - Service request form
- `modules.html` - Elevator modules showcase

### Admin Pages
- `admin.html` - Admin login page
- `dashboard.html` - Admin dashboard for management

## Folder Structure

```
elevator-services/
├── css/
│   └── style.css              # Complete styling for all pages
├── js/
│   └── app.js                 # JavaScript functionality and LocalStorage management
├── index.html                 # Home page
├── service.html               # Service request form
├── modules.html               # Elevator modules display
├── admin.html                 # Admin login
├── dashboard.html             # Admin dashboard
└── README.md                  # This file
```

## Default Admin Credentials

- **Username:** admin
- **Password:** admin123

## Data Storage

All data is stored in browser LocalStorage:
- Service requests are saved as JSON objects
- Elevator modules are stored and dynamically loaded
- Admin credentials are stored (for demo purposes)

## Features Details

### Service Request Form
- Fields: Name, Phone, Email, Location, Elevator Type, Problem Description
- Form validation with error messages
- Success confirmation with request ID
- Data saved to LocalStorage

### Admin Dashboard
- View all service requests in a table format
- Update request status (Pending/In Progress/Completed)
- Add, edit, and delete elevator modules
- Real-time data updates

### Module Management
- Pre-loaded with 4 default elevator types:
  - Passenger Lift
  - Goods Lift
  - Hospital Lift
  - Home Lift
- Each module includes: Title, Description, Capacity, Speed, Features

## Browser Compatibility

Works in all modern browsers:
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Notes

- This is a demo/college project version
- Data persists only in the current browser
- Clearing browser data will reset all information
- For production use, consider server-side implementation

## Development

The code is beginner-friendly with:
- Clear comments throughout
- Modular JavaScript functions
- Organized CSS with responsive design
- Simple HTML structure
