# Modern Badge Design System

## Overview
All badges across the BD CRM project now use a consistent, modern design with soft colors and rounded pill shapes.

## Badge Variants

### Success (Green)
```html
<span class="badge bg-success">Active</span>
<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
```
- Background: Light green (#e8f5e9)
- Text: Dark green (#2e7d32)
- Use for: Active status, completed items, success states

### Danger (Red)
```html
<span class="badge bg-danger">Deactivated</span>
<span class="badge bg-danger"><i class="fas fa-ban me-1"></i>Inactive</span>
```
- Background: Light red (#ffebee)
- Text: Dark red (#c62828)
- Use for: Errors, deactivated accounts, warnings

### Primary (Purple)
```html
<span class="badge bg-primary">10</span>
<span class="badge bg-primary">Premium</span>
```
- Background: Light purple (#e8eaf6)
- Text: Dark purple (#5e35b1)
- Use for: Counts, primary information

### Info (Blue)
```html
<span class="badge bg-info">Interviewing</span>
<span class="badge bg-info"><i class="fas fa-video me-1"></i>Interview</span>
```
- Background: Light blue (#e1f5fe)
- Text: Dark blue (#0277bd)
- Use for: Informational states, interviewing status

### Warning (Orange)
```html
<span class="badge bg-warning">Pending</span>
<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Almost There</span>
```
- Background: Light yellow (#fff8e1)
- Text: Dark orange (#f57c00)
- Use for: Pending items, warnings, near completion

### Secondary (Gray)
```html
<span class="badge bg-secondary">Submitted</span>
<span class="badge bg-secondary">Draft</span>
```
- Background: Light gray (#f5f5f5)
- Text: Medium gray (#616161)
- Use for: Neutral states, submitted status

## Design Specifications

- **Font Size**: 0.75rem (12px)
- **Font Weight**: 500 (Medium)
- **Padding**: 0.2rem vertical, 0.5rem horizontal (compact design)
- **Border Radius**: 5px (slightly rounded)
- **Letter Spacing**: 0.3px
- **Display**: inline-flex (for better icon alignment)

## Usage Examples

### With Icons
```html
<span class="badge bg-success">
    <i class="fas fa-check-circle me-1"></i>Goal Achieved
</span>
```

### Rounded Pill (Extra Rounded)
```html
<span class="badge bg-primary rounded-pill">Badge Text</span>
```

### In Tables
```html
<td>
    <span class="badge bg-{{ $status === 'active' ? 'success' : 'secondary' }}">
        {{ ucfirst($status) }}
    </span>
</td>
```

## Color Palette Reference

| Variant   | Background | Text Color | Hex Background | Hex Text |
|-----------|-----------|-----------|----------------|----------|
| Success   | Light Green | Dark Green | #e8f5e9 | #2e7d32 |
| Danger    | Light Red | Dark Red | #ffebee | #c62828 |
| Primary   | Light Purple | Dark Purple | #e8eaf6 | #5e35b1 |
| Info      | Light Blue | Dark Blue | #e1f5fe | #0277bd |
| Warning   | Light Yellow | Dark Orange | #fff8e1 | #f57c00 |
| Secondary | Light Gray | Medium Gray | #f5f5f5 | #616161 |
| Light     | Very Light Gray | Dark Gray | #fafafa | #424242 |
| Dark      | Dark Blue Gray | White | #37474f | #ffffff |

## Applied Throughout

The badge design system is now applied across:
- ✅ Admin Dashboard
- ✅ Goals Management
- ✅ Proposals Management
- ✅ User Management
- ✅ BD Dashboard
- ✅ All Tables and Lists
- ✅ Status Indicators
- ✅ Counts and Metrics

All existing badges will automatically inherit this new design!
