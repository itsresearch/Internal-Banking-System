# System Architecture & Flow Diagrams

## User Journey Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                     APPLICATION ENTRY POINT                     │
│                         GET / (Home)                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │ User Logged In? │
                    └────┬────────┬───┘
                    Yes │         │ No
              ┌─────────┘         └──────────────┐
              ▼                                  ▼
    ┌──────────────────┐          ┌──────────────────────┐
    │ Check User Role  │          │ Show Login/Register  │
    └────┬────────┬───┘           │       Form           │
         │        │               └──────────────────────┘
      ┌──┴──┬─────┴──┬───┐
      ▼     ▼        ▼   ▼
   Admin Manager  Staff Default
      │     │        │     │
      ▼     ▼        ▼     ▼
    /admin /mngr  /staff /dashboard
```

---

## Registration Process

```
START (Visitor)
│
├─ Click "Register"
│
├─ Navigate to /register
│
├─ See Form:
│  ├─ Name Field
│  ├─ Email Field
│  ├─ Password Field
│  ├─ Role Selection (NEW!)
│  │  └─ Admin (ID: 1)
│  │  └─ Manager (ID: 2)
│  │  └─ Staff (ID: 3)
│  └─ Submit Button
│
├─ Fill Form & Submit
│  └─ POST /register
│
├─ Validation (CreateNewUser)
│  ├─ ✓ Name required & string
│  ├─ ✓ Email unique & valid
│  ├─ ✓ Password meets rules
│  ├─ ✓ Role ID exists in DB (NEW!)
│  └─ ✓ Terms agreed (if enabled)
│
├─ If Valid:
│  ├─ Hash password
│  ├─ Create User record with role_id (NEW!)
│  ├─ Auto-login (Fortify handles)
│  ├─ FortifyServiceProvider redirect logic:
│  │  ├─ Fetch user with role
│  │  ├─ Check role name
│  │  └─ Return appropriate route
│  └─ Browser Redirect
│     ├─ Admin → /dashboard/admin → body.blade.php
│     ├─ Manager → /dashboard/manager → manager.blade.php
│     ├─ Staff → /dashboard/staff → staff.blade.php
│     └─ Unknown → /dashboard
│
├─ User Logged In → Dashboard
│
END (Authenticated User on Role Dashboard)
```

---

## Login Process

```
START (Visitor)
│
├─ Click "Login"
│
├─ Navigate to /login
│
├─ See Form:
│  ├─ Email Field
│  ├─ Password Field
│  └─ Submit Button
│
├─ Fill Form & Submit
│  └─ POST /login
│
├─ Fortify Authenticate:
│  ├─ Query users table
│  ├─ Find user by email
│  ├─ Verify password hash
│  └─ Create session
│
├─ If Valid:
│  ├─ Session created
│  ├─ FortifyServiceProvider redirect triggered
│  ├─ Get authenticated user
│  ├─ Fetch user.role relationship
│  ├─ Check role.name
│  └─ Return appropriate route
│     ├─ role.name === 'admin'
│     │  └─ → /dashboard/admin
│     ├─ role.name === 'manager'
│     │  └─ → /dashboard/manager
│     ├─ role.name === 'staff'
│     │  └─ → /dashboard/staff
│     └─ no role
│        └─ → /dashboard (fallback)
│
├─ Browser Redirect
│  └─ Load Role Dashboard
│
├─ User Logged In → Dashboard
│
END (Authenticated User on Role Dashboard)
```

---

## Database Relationships

```
┌──────────────┐              ┌──────────────┐
│    ROLES     │ 1 ──────── N │    USERS     │
├──────────────┤              ├──────────────┤
│ id (PK)      │◄──────────┐  │ id (PK)      │
│ name         │           └──┤ role_id (FK) │
│ description  │              │ name         │
│ created_at   │              │ email        │
│ updated_at   │              │ password     │
└──────────────┘              │ status       │
                              │ created_at   │
                              │ updated_at   │
                              └──────────────┘

Relationship:
  One Role → Many Users (1:N)
  One User → One Role (N:1)

Constraint:
  user.role_id FOREIGN KEY → role.id
  ON DELETE CASCADE
```

---

## Code Flow: Redirect After Login

```
┌─ FortifyServiceProvider::boot()
│
├─ Fortify::redirects('login', function() { ... })
│
├─ Anonymous Function Executed:
│  │
│  ├─ $user = auth()->user();
│  │  └─ Get authenticated user object
│  │
│  ├─ $role = $user->role ? $user->role->name : null;
│  │  ├─ Fetch role relationship
│  │  ├─ If exists: get role name
│  │  └─ If not: set to null
│  │
│  ├─ if ($role === 'admin')
│  │  └─ return redirect()->route('dashboard.admin');
│  │
│  ├─ elseif ($role === 'manager')
│  │  └─ return redirect()->route('dashboard.manager');
│  │
│  ├─ elseif ($role === 'staff')
│  │  └─ return redirect()->route('dashboard.staff');
│  │
│  └─ return redirect('/dashboard'); // Fallback
│
└─ Fortify Applies Redirect Automatically
   └─ Browser receives redirect response
      └─ User navigated to role dashboard
```

---

## Route Hierarchy

```
/ (Home)
├─ GET / → Check auth & role → Redirect or show login
│
├─ /login (via Fortify)
│  ├─ GET /login → Show login form
│  └─ POST /login → Process login → Redirect by role
│
├─ /register (via Fortify)
│  ├─ GET /register → Show registration form with role dropdown
│  └─ POST /register → Validate role & create user → Auto-login → Redirect by role
│
├─ /logout (via Fortify)
│  └─ POST /logout → Destroy session → Redirect to /
│
└─ /dashboard/* (Protected by auth middleware)
   ├─ GET /dashboard → Alias for /dashboard/admin
   ├─ GET /dashboard/admin → Show admin dashboard (body.blade.php)
   ├─ GET /dashboard/manager → Show manager dashboard (manager.blade.php)
   └─ GET /dashboard/staff → Show staff dashboard (staff.blade.php)
      └─ All require: auth:sanctum, jetstream.auth_session, verified
         └─ If not authenticated → Redirect to /login
```

---

## View Component Structure

```
registration.blade.php
├─ x-guest-layout
│  └─ x-authentication-card
│     ├─ Logo slot
│     ├─ Validation errors display
│     └─ Form (POST /register)
│        ├─ Name input
│        ├─ Email input
│        ├─ Password input
│        ├─ Confirm password input
│        ├─ Role selection dropdown (NEW!)
│        │  └─ Options: Admin, Manager, Staff
│        ├─ Terms checkbox
│        └─ Submit button

Dashboard views (body.blade.php, manager.blade.php, staff.blade.php)
├─ Header component
├─ Sidebar component
├─ Content wrapper
│  └─ Role-specific content
├─ Footer
└─ Scripts

All dashboards share:
  ├─ Same header
  ├─ Same sidebar
  ├─ Same footer
  └─ Different body content
```

---

## State Transitions

```
                    ┌────────────────────┐
                    │   SESSION START    │
                    └──────────┬─────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        ▼                     ▼                     ▼
   ┌──────────┐       ┌──────────────┐      ┌──────────┐
   │ NOT AUTH │       │  REGISTERED  │      │ LOGGED   │
   │ (Guest)  │◄──────┤   (In DB)    │◄─────┤   IN     │
   └─────┬────┘   Logout├──────────────┤  Redirect└──┬────┘
         │              │               │            │
   Click │              │               │            │
   Register/Login        ├──────┬───────┤        Logout│
         │          [Email]    │       │            │
         └─────────────┼────────┤ Verify│            │
                       │        │ Email │            │
                  POST /register│       │            │
                  Validate role ▼       ▼            │
                  Create User   [Validated]          │
                       │                             │
                  Auto-Login    ▼                     │
                  (Fortify) ┌─────────────┐          │
                       └───►│ Redirect    │◄─────────┘
                            │ by Role     │
                            └──────┬──────┘
                                   │
                    ┌──────────────┼──────────────┐
                    ▼              ▼              ▼
            ┌──────────────┐ ┌──────────────┐ ┌────────────┐
            │ Admin        │ │ Manager      │ │ Staff      │
            │ Dashboard    │ │ Dashboard    │ │ Dashboard  │
            └──────────────┘ └──────────────┘ └────────────┘
```

---

## Data Validation Flow

```
Registration Form Submission
       │
       ├─ Client-side validation
       │  └─ HTML5 required attribute
       │
       ├─ POST /register to server
       │
       ├─ Server-side validation (CreateNewUser::create)
       │  │
       │  ├─ Validator::make($input, [
       │  │    'name' => ['required', 'string', 'max:255'],
       │  │    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
       │  │    'password' => [passwordRules()],
       │  │    'role_id' => ['required', 'integer', 'exists:roles,id'], ← NEW!
       │  │    'terms' => [conditional]
       │  │  ])
       │  │
       │  ├─ Check 'role_id' exists:roles
       │  │  └─ Query roles table for matching ID
       │  │
       │  └─ All validation passes?
       │
       ├─ If Valid:
       │  └─ Create user with all fields including role_id
       │
       ├─ If Invalid:
       │  └─ Return with error messages
       │     └─ User corrects and resubmits
       │
       └─ Success → Auto-login → Redirect by role
```

---

## Security Layers

```
REQUEST
  │
  ├─ Layer 1: CSRF Protection
  │  ├─ @csrf token in form
  │  └─ Middleware validates token
  │
  ├─ Layer 2: Input Validation
  │  ├─ Type checking (integer, string)
  │  ├─ Format validation (email)
  │  └─ Database integrity (exists:roles,id)
  │
  ├─ Layer 3: SQL Injection Prevention
  │  ├─ Use Eloquent ORM (parameterized queries)
  │  └─ Never raw SQL with user input
  │
  ├─ Layer 4: XSS Prevention
  │  ├─ Blade templating escapes output
  │  └─ {{ $variable }} escaped
  │
  ├─ Layer 5: Password Security
  │  ├─ Hash::make() - bcrypt hashing
  │  └─ Hash::check() - verification
  │
  ├─ Layer 6: Session Security
  │  ├─ Secure session tokens
  │  ├─ HttpOnly cookies
  │  └─ SameSite protection
  │
  ├─ Layer 7: Authentication Middleware
  │  ├─ Check user is authenticated
  │  └─ Check valid session
  │
  └─ Layer 8: Foreign Key Constraints
     ├─ role_id must reference existing role
     └─ Prevents orphaned records
```

---

## Performance Considerations

```
Optimization Opportunities:
  │
  ├─ Eager Loading
  │  ├─ User::with('role')->find($id)
  │  └─ Prevents N+1 query problems
  │
  ├─ Query Caching
  │  ├─ Cache::remember('roles', 86400, fn() => Role::all())
  │  └─ 24-hour cache for roles
  │
  ├─ Database Indexing
  │  ├─ Index on users.role_id
  │  ├─ Index on users.email
  │  └─ Faster lookups
  │
  ├─ Session Optimization
  │  ├─ Use database session driver
  │  └─ Garbage collection configured
  │
  └─ View Caching
     ├─ Blade views compiled to PHP
     └─ Subsequent requests use cached views
```

---

## Error Handling

```
Authentication Errors:
  │
  ├─ Invalid Email/Password
  │  └─ Return to login with error message
  │
  ├─ Invalid Role ID
  │  └─ Return to registration with validation error
  │
  ├─ Database Connection Error
  │  └─ Laravel error page or custom handler
  │
  ├─ Session Expiration
  │  └─ Redirect to login
  │
  ├─ Missing Role Relationship
  │  └─ Fallback redirect to /dashboard
  │
  └─ Unauthorized Access
     └─ Redirect to login (middleware)
```
