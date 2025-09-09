# GORA Routing System - Complete Guide

## Overview
The application now uses a modern MVC routing system with a single entry point (`index.php`) and clean URLs. All requests are routed through the main router, providing better security, maintainability, and user experience.

## Key Components

### 1. Main Entry Point (`index.php`)
- **Purpose**: Single entry point for all requests
- **Features**:
  - Session management
  - Database connection
  - Router initialization
  - Request handling

### 2. URL Rewriting (`.htaccess`)
- **Purpose**: Redirects all requests to `index.php`
- **Security**: Protects sensitive directories and files
- **Performance**: Enables caching for static assets

### 3. Router Class
- **Purpose**: Handles URL routing and view loading
- **Features**:
  - Route mapping
  - Controller execution
  - View loading
  - 404 error handling

## URL Structure

### Before (Old System)
```
http://localhost/GORA/views/login.php
http://localhost/GORA/views/dashboard.php
http://localhost/GORA/views/CRUDS/usuarios.php
```

### After (New System)
```
http://localhost/GORA/login
http://localhost/GORA/dashboard
http://localhost/GORA/usuarios
```

## Complete Use Case Flow

### 1. User Access Flow

#### Scenario: User visits the application
1. **User enters URL**: `http://localhost/GORA/`
2. **Apache processes request**: `.htaccess` redirects to `index.php`
3. **Router processes**: Empty path defaults to `/login`
4. **View loaded**: `views/login.php` is included
5. **User sees**: Login form

#### Scenario: User logs in successfully
1. **User submits form**: POST to `/login`
2. **Router processes**: Calls `AuthController->login()`
3. **Authentication**: User credentials verified
4. **Session created**: User data stored in session
5. **Redirect**: Based on user level:
   - Admin/Level 4: Redirected to `/dashboard`
   - Others: Redirected to `/listas`

#### Scenario: User navigates to dashboard
1. **User clicks link**: `/dashboard`
2. **Router processes**: Calls `AuthController->showDashboard()`
3. **Auth check**: Verifies user is logged in
4. **View loaded**: `views/dashboard.php` included
5. **AJAX calls**: Load statistics and student lists via `/estadisticas` and `/listas`

### 2. Navigation Flow

#### Scenario: Admin user navigation
1. **Dashboard**: `/dashboard` - Main overview with statistics
2. **Students**: `/listas` - Student management
3. **Statistics**: `/estadisticas` - Analytics and reports
4. **Follow-ups**: `/seguimientos` - Student follow-up management
5. **Management**:
   - `/usuarios` - User management
   - `/alumnos` - Student CRUD
   - `/carreras` - Career management
   - `/grupos` - Group management
   - `/modalidades` - Modality management
   - `/tipo-seguimiento` - Follow-up type management

#### Scenario: Regular user navigation
1. **Students**: `/listas` - Student lists
2. **Follow-ups**: `/seguimientos` - Follow-up management
3. **Profile**: `/profile` - User profile

### 3. Security Flow

#### Scenario: Unauthorized access attempt
1. **User tries to access**: `/dashboard` without login
2. **Auth check**: `auth_check.php` detects no session
3. **Redirect**: User sent to `/login`
4. **Error message**: "No tienes los permisos necesarios"

#### Scenario: Permission-based access
1. **User accesses**: `/usuarios` (Admin only)
2. **Permission check**: User level verified
3. **Access granted/denied**: Based on user level
4. **Error handling**: Unauthorized users redirected to login

### 4. AJAX Request Flow

#### Scenario: Dashboard loads statistics
1. **Page loads**: `/dashboard`
2. **JavaScript executes**: Fetch request to `/estadisticas?modo=componente`
3. **Router processes**: Loads `views/estadisticas.php`
4. **Response**: HTML content returned
5. **DOM update**: Statistics displayed

#### Scenario: Student list pagination
1. **User clicks**: Pagination button
2. **JavaScript executes**: Fetch to `/alumnos-paginados?action=load_students&id_grupo=X&page=Y`
3. **Router processes**: Loads `views/alumnos_paginados.php`
4. **Response**: JSON with HTML content
5. **DOM update**: Student list updated

### 5. Error Handling Flow

#### Scenario: 404 Error
1. **User enters**: Invalid URL like `/invalid-page`
2. **Router processes**: No matching route found
3. **404 response**: HTTP 404 status code
4. **Error page**: `views/404.php` displayed
5. **User action**: Can return to home via link

#### Scenario: Server Error
1. **Error occurs**: Database connection fails
2. **Error handling**: PHP error caught
3. **User experience**: Graceful error message
4. **Logging**: Error logged for debugging

## File Structure Changes

### Modified Files
- `index.php` - New main entry point
- `.htaccess` - URL rewriting rules
- `views/objects/header.php` - Updated asset paths
- `views/objects/footer.php` - Updated asset paths
- `views/objects/sidebar.php` - Updated navigation links
- `views/objects/navbar.php` - Updated logout link
- `views/objects/auth_check.php` - Updated redirect paths
- `controllers/authController.php` - Added new methods
- All view files - Removed `session_start()` calls

### New Files
- `views/404.php` - Error page
- `ROUTING_GUIDE.md` - This documentation

## Benefits of New System

### 1. Security
- Single entry point prevents direct file access
- Sensitive directories protected by `.htaccess`
- Consistent authentication across all pages

### 2. Maintainability
- Centralized routing logic
- Clean URL structure
- Easier to add new routes

### 3. User Experience
- Clean, SEO-friendly URLs
- Consistent navigation
- Better error handling

### 4. Performance
- Optimized asset loading
- Caching headers for static files
- Reduced server load

## Testing the System

### 1. Basic Navigation
- Visit `http://localhost/GORA/` → Should redirect to login
- Login with valid credentials → Should redirect to dashboard
- Navigate through sidebar links → Should work correctly

### 2. Security Testing
- Try accessing `/dashboard` without login → Should redirect to login
- Test different user levels → Should show appropriate pages
- Try invalid URLs → Should show 404 page

### 3. AJAX Functionality
- Dashboard statistics should load automatically
- Student list pagination should work
- All forms should submit correctly

## Troubleshooting

### Common Issues
1. **404 errors**: Check `.htaccess` is in root directory
2. **Asset loading issues**: Verify paths in header.php and footer.php
3. **Session issues**: Ensure `session_start()` is only called in index.php
4. **Permission errors**: Check file permissions on server

### Debug Mode
- Check Apache error logs
- Enable PHP error reporting
- Verify database connection
- Test individual routes

This routing system provides a solid foundation for the GORA application with improved security, maintainability, and user experience.
