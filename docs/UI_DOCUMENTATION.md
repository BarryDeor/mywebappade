# HR & Payroll Management System - UI Documentation

## Overview

The HR & Payroll Management System features a modern, clean, and intuitive user interface built with React, TypeScript, and Tailwind CSS. The UI is designed to be role-based, secure, scalable, and easy to use for both HR administrators and employees.

## Design Principles

### 1. Professional Enterprise Look
- Clean, minimalist design with consistent spacing
- Professional color palette (Blue primary, Gray secondary)
- Modern typography using Inter font family
- Subtle shadows and borders for depth

### 2. Intuitive Navigation
- Role-based sidebar navigation
- Clear visual hierarchy
- Breadcrumb navigation for deep pages
- Quick action buttons on dashboards

### 3. Responsive Design
- Mobile-first approach
- Breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px)
- Flexible grid layouts
- Touch-friendly interactive elements

### 4. Accessibility
- WCAG 2.1 AA compliant
- Keyboard navigation support
- Screen reader friendly
- High contrast ratios
- Focus indicators

## Color System

### Primary Colors (Blue)
- `primary-50`: #eff6ff - Lightest backgrounds
- `primary-100`: #dbeafe - Hover states
- `primary-500`: #3b82f6 - Default primary
- `primary-600`: #2563eb - Primary buttons
- `primary-700`: #1d4ed8 - Active states

### Secondary Colors (Gray)
- `secondary-50`: #f8fafc - Page backgrounds
- `secondary-100`: #f1f5f9 - Card backgrounds
- `secondary-600`: #475569 - Body text
- `secondary-900`: #0f172a - Headings

### Semantic Colors
- **Success**: Green (#10b981)
- **Warning**: Yellow (#f59e0b)
- **Danger**: Red (#ef4444)
- **Info**: Blue (#3b82f6)

## Typography

### Font Family
- Primary: Inter (Google Fonts)
- Fallback: system-ui, sans-serif

### Font Sizes
- `text-xs`: 0.75rem (12px)
- `text-sm`: 0.875rem (14px)
- `text-base`: 1rem (16px)
- `text-lg`: 1.125rem (18px)
- `text-xl`: 1.25rem (20px)
- `text-2xl`: 1.5rem (24px)
- `text-3xl`: 1.875rem (30px)

### Font Weights
- `font-normal`: 400
- `font-medium`: 500
- `font-semibold`: 600
- `font-bold`: 700

## Component Library

### 1. Button Component

**Variants:**
- `primary`: Blue background, white text
- `secondary`: Gray background, white text
- `outline`: Transparent with border
- `ghost`: Transparent, minimal styling
- `danger`: Red background, white text

**Sizes:**
- `sm`: Small (px-3 py-1.5)
- `md`: Medium (px-4 py-2) - Default
- `lg`: Large (px-6 py-3)

**States:**
- Default
- Hover
- Active
- Disabled
- Loading (with spinner)

**Usage:**
```tsx
<Button variant="primary" size="md" onClick={handleClick}>
  Click Me
</Button>

<Button variant="outline" isLoading={true}>
  Processing...
</Button>
```

### 2. Input Component

**Features:**
- Label support
- Error messages
- Helper text
- Required indicator
- Disabled state
- Various input types

**Usage:**
```tsx
<Input
  label="Email Address"
  type="email"
  placeholder="you@company.com"
  error="Invalid email"
  required
/>
```

### 3. Card Component

**Sub-components:**
- `Card`: Container
- `CardHeader`: Header section
- `CardTitle`: Title text
- `CardContent`: Main content

**Padding Options:**
- `none`: No padding
- `sm`: Small (p-4)
- `md`: Medium (p-6) - Default
- `lg`: Large (p-8)

**Usage:**
```tsx
<Card>
  <CardHeader>
    <CardTitle>Dashboard</CardTitle>
  </CardHeader>
  <CardContent>
    Content goes here
  </CardContent>
</Card>
```

### 4. Table Component

**Features:**
- Custom column definitions
- Row click handlers
- Loading state
- Empty state
- Responsive overflow

**Usage:**
```tsx
<Table
  data={employees}
  columns={[
    { key: 'name', header: 'Name' },
    { key: 'email', header: 'Email' },
    { 
      key: 'status', 
      header: 'Status',
      render: (item) => <Badge>{item.status}</Badge>
    }
  ]}
  onRowClick={(item) => console.log(item)}
/>
```

### 5. Modal Component

**Features:**
- Backdrop overlay
- Escape key to close
- Custom sizes (sm, md, lg, xl)
- Optional close button
- Scroll lock

**Usage:**
```tsx
<Modal
  isOpen={showModal}
  onClose={() => setShowModal(false)}
  title="Add Employee"
  size="lg"
>
  <form>...</form>
</Modal>
```

### 6. Badge Component

**Variants:**
- `default`: Gray
- `success`: Green
- `warning`: Yellow
- `danger`: Red
- `info`: Blue

**Usage:**
```tsx
<Badge variant="success">Active</Badge>
<Badge variant="warning">Pending</Badge>
```

### 7. Avatar Component

**Features:**
- Image support
- Initials fallback
- Multiple sizes (sm, md, lg, xl)
- Circular design

**Usage:**
```tsx
<Avatar
  firstName="John"
  lastName="Doe"
  src="/path/to/image.jpg"
  size="md"
/>
```

### 8. Select Component

**Features:**
- Label support
- Error messages
- Options array
- Required indicator

**Usage:**
```tsx
<Select
  label="Department"
  options={[
    { value: 'eng', label: 'Engineering' },
    { value: 'sales', label: 'Sales' }
  ]}
  required
/>
```

## Layout Components

### 1. Sidebar

**Features:**
- Role-based navigation items
- Active state highlighting
- Icon + text labels
- Collapsible (future enhancement)

**Navigation Items:**
- Dashboard
- Employees (Admin only)
- Payroll (Admin only)
- Attendance
- Leave Management
- Performance
- Recruitment (Admin only)
- Reports (Admin only)
- My Profile (Employee only)

### 2. Header

**Features:**
- Welcome message with user name
- Role display
- Notification bell with badge
- User dropdown menu
- Logout functionality

**Dropdown Menu Items:**
- My Profile
- Settings
- Logout

### 3. MainLayout

**Structure:**
```
┌─────────────────────────────────────┐
│           Sidebar (fixed)           │
│  ┌──────────────────────────────┐   │
│  │         Header (sticky)      │   │
│  ├──────────────────────────────┤   │
│  │                              │   │
│  │      Main Content Area       │   │
│  │      (scrollable)            │   │
│  │                              │   │
│  └──────────────────────────────┘   │
└─────────────────────────────────────┘
```

## Page Layouts

### Admin Dashboard

**Sections:**
1. **Stats Cards** (4 columns)
   - Total Employees
   - Active Employees
   - On Leave
   - Pending Approvals

2. **Charts** (2 columns)
   - Employees by Department (Bar Chart)
   - Payroll Trend (Line Chart)

3. **Recent Activities** (Full width)
   - Activity feed with icons and timestamps

### Employee Management Page

**Features:**
- Search bar
- Add Employee button
- Employee table with:
  - Avatar + Name
  - Email
  - Department
  - Position
  - Status badge
  - Hire date
- Click to view details modal
- Add employee modal

### Payroll Page

**Features:**
- Summary cards (3 columns)
  - Current Month Payroll
  - Average Salary
  - Total Deductions
- Payroll history table
- New payroll run button
- Payroll details modal

### Employee Dashboard (Self-Service)

**Sections:**
1. **Quick Actions** (4 columns)
   - Request Leave
   - Clock In/Out
   - View Payslips
   - Update Profile

2. **Leave Balance** (Card)
   - Progress bars for each leave type

3. **Upcoming Leaves** (Card)
   - List of approved/pending leaves

4. **Recent Payslips** (Card)
   - Downloadable payslip cards

### My Leaves Page

**Features:**
- Leave balance summary (3 cards)
- Request leave button
- Leave history table
- Request leave modal
- Leave details modal

## Responsive Behavior

### Mobile (< 768px)
- Sidebar collapses to hamburger menu
- Single column layouts
- Stacked cards
- Horizontal scroll for tables
- Touch-friendly buttons (min 44px)

### Tablet (768px - 1024px)
- 2-column grid layouts
- Visible sidebar (collapsible)
- Optimized table columns

### Desktop (> 1024px)
- Full sidebar always visible
- 3-4 column grid layouts
- All table columns visible
- Hover states enabled

## Interaction Patterns

### Loading States
- Spinner for async operations
- Skeleton screens for initial loads
- Button loading state with spinner
- Table loading overlay

### Empty States
- Friendly messages
- Relevant icons
- Call-to-action buttons
- Helpful suggestions

### Error States
- Inline validation errors
- Toast notifications for global errors
- Error boundaries for crashes
- Retry mechanisms

### Success States
- Toast notifications
- Success badges
- Confirmation modals
- Visual feedback (checkmarks)

## Animation & Transitions

### Hover Effects
- Button: Background color change
- Card: Subtle shadow increase
- Table row: Background highlight
- Link: Color change

### Transitions
- Duration: 150-300ms
- Easing: ease-in-out
- Properties: colors, shadows, transforms

### Modal Animations
- Backdrop fade-in
- Modal slide-up
- Smooth close

## Accessibility Features

### Keyboard Navigation
- Tab order follows visual flow
- Focus indicators on all interactive elements
- Escape key closes modals
- Enter key submits forms

### Screen Readers
- Semantic HTML elements
- ARIA labels where needed
- Alt text for images
- Role attributes

### Color Contrast
- Text: Minimum 4.5:1 ratio
- Large text: Minimum 3:1 ratio
- Interactive elements: Clear focus states

## Performance Optimization

### Code Splitting
- Route-based splitting
- Lazy loading for heavy components
- Dynamic imports

### Asset Optimization
- Image compression
- SVG icons (no icon libraries)
- Font subsetting
- Gzip compression

### Rendering Optimization
- React.memo for expensive components
- useMemo for computed values
- useCallback for event handlers
- Virtual scrolling for large lists

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Future Enhancements

### Planned Features
- Dark mode toggle
- Customizable themes
- Advanced data visualization
- Real-time notifications
- Drag-and-drop interfaces
- Advanced filtering and search
- Export to PDF/Excel
- Bulk operations
- Keyboard shortcuts
- Offline support

### Accessibility Improvements
- High contrast mode
- Font size adjustment
- Reduced motion mode
- Voice commands

## Development Guidelines

### Component Creation
1. Use TypeScript for type safety
2. Follow naming conventions (PascalCase for components)
3. Extract reusable logic to hooks
4. Document props with JSDoc
5. Write accessible markup

### Styling Guidelines
1. Use Tailwind utility classes
2. Avoid custom CSS when possible
3. Use cn() utility for conditional classes
4. Follow mobile-first approach
5. Maintain consistent spacing

### State Management
1. Use Zustand for global state
2. Local state for component-specific data
3. React Query for server state (future)
4. Context for theme/locale

### Testing (Future)
1. Unit tests for utilities
2. Component tests with React Testing Library
3. E2E tests with Playwright
4. Visual regression tests

## Conclusion

The HR & Payroll Management System UI provides a modern, professional, and user-friendly interface that scales from small teams to large enterprises. The component-based architecture ensures consistency, maintainability, and extensibility for future enhancements.
