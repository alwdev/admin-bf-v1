---
name: Withdrawal Copy Button Feature
description: Add copy button to withdrawal list for copying account details (name, number, bank, amount) to clipboard
type: feature
date: 2026-06-01
---

# Withdrawal Copy Button Feature Specification

## Overview

Add a copy button to the withdrawal transaction list that allows admins to quickly copy withdrawal account details (account name, account number, bank name, and amount) to clipboard for sharing in chat applications.

## Requirements

### Functional Requirements

1. **Copy Button Display**
   - Show copy icon in the "ปลายทาง" (destination) column
   - Display only for withdrawal transactions (`type = 'withdraw'`)
   - Display for all withdrawal statuses (pending, approved, rejected)
   - Hide button when withdrawal bank data is empty (`withdrawBankEmpty = true`)

2. **Copy Format**
   - Use compact format with Thai labels:
     ```
     ชื่อ: [account_name]
     เลขบัญชี: [bank_number]
     ธนาคาร: [bank_name]
     ยอดเงิน: [amount formatted with 2 decimals]
     ```
   - Example:
     ```
     ชื่อ: สมชาย ใจดี
     เลขบัญชี: 1234567890
     ธนาคาร: ไทยพาณิชย์
     ยอดเงิน: 1,500.00
     ```

3. **User Feedback**
   - Show toast notification "คัดลอกแล้ว!" on successful copy
   - Toast appears at top-right corner
   - Auto-dismiss after 1.5 seconds
   - No confirmation needed before copying

### Non-Functional Requirements

1. **Browser Compatibility**
   - Use Clipboard API (`navigator.clipboard.writeText()`)
   - Requires HTTPS or localhost (project runs on `127.0.0.1:8098` - compatible)
   - Target modern browsers: Chrome, Firefox, Safari, Edge (latest versions)

2. **Performance**
   - Copy operation should be instant (< 100ms)
   - No server-side request needed
   - All data available in DOM via data attributes

3. **Accessibility**
   - Button should be keyboard accessible
   - Proper hover states for visual feedback
   - Icon should be recognizable as copy action

## Design Specifications

### UI Components

**Copy Button**
- Icon: Boxicons `bx-copy`
- Size: 18px icon, 32x32px clickable area
- Default color: `#adb5bd` (light gray)
- Hover color: `#E3A941` (theme gold)
- Position: Right side of account info in "ปลายทาง" column
- Padding: 6px
- Transition: color 0.2s ease

**Toast Notification**
- Library: SweetAlert2 (already in project)
- Type: Toast
- Position: top-end
- Icon: success
- Title: "คัดลอกแล้ว!"
- Duration: 1500ms
- No confirm button

### Layout

```
┌─────────────────────────────────────────┐
│ ปลายทาง (Destination Column)            │
├─────────────────────────────────────────┤
│ [Bank Icon] 1234567890                  │
│ สมชาย ใจดี                    [📋 Copy] │
└─────────────────────────────────────────┘
```

## Technical Implementation

### File Changes

**File:** `resources/views/transaction/list.blade.php`

**Changes:**
1. Add CSS for copy button styling
2. Modify "ปลายทาง" column HTML for withdraw rows
3. Add JavaScript function for copy operation
4. Add toast notification function

### HTML Structure

```html
@elseif($item->type == 'withdraw')
    @if ($withdrawBankEmpty)
        <span class="text-muted">—</span>
    @else
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <x-bank-icon :bank-name="$item->bank_name" />
                {{ $item->bank_number }} <br>
                {{ $item->account_name }}
            </div>
            <button type="button" 
                    class="btn-copy-withdraw" 
                    onclick="copyWithdrawInfo(this)"
                    data-account-name="{{ $item->account_name }}"
                    data-bank-number="{{ $item->bank_number }}"
                    data-bank-name="{{ $item->bank_name }}"
                    data-amount="{{ number_format((float) $item->amount, 2) }}">
                <i class="bx bx-copy"></i>
            </button>
        </div>
    @endif
@endif
```

### CSS Styling

```css
.btn-copy-withdraw {
    background: none;
    border: none;
    padding: 6px;
    cursor: pointer;
    color: #adb5bd;
    transition: color 0.2s;
    margin-left: 8px;
    line-height: 1;
}

.btn-copy-withdraw:hover {
    color: #E3A941;
}

.btn-copy-withdraw:focus {
    outline: none;
}

.btn-copy-withdraw i {
    font-size: 18px;
}
```

### JavaScript Functions

```javascript
function copyWithdrawInfo(button) {
    const accountName = button.dataset.accountName;
    const bankNumber = button.dataset.bankNumber;
    const bankName = button.dataset.bankName;
    const amount = button.dataset.amount;
    
    const text = `ชื่อ: ${accountName}\nเลขบัญชี: ${bankNumber}\nธนาคาร: ${bankName}\nยอดเงิน: ${amount}`;
    
    navigator.clipboard.writeText(text)
        .then(() => {
            showCopyToast();
        })
        .catch(err => {
            console.error('Copy failed:', err);
            // Fallback: show error message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'ไม่สามารถคัดลอกได้',
                showConfirmButton: false,
                timer: 1500
            });
        });
}

function showCopyToast() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'คัดลอกแล้ว!',
        showConfirmButton: false,
        timer: 1500
    });
}
```

## Data Flow

1. User clicks copy button on withdrawal row
2. JavaScript reads data attributes from button element
3. Format text string with Thai labels
4. Call `navigator.clipboard.writeText()` with formatted text
5. On success: show success toast
6. On error: show error toast and log to console

## Edge Cases & Error Handling

### Edge Cases

1. **Empty withdrawal data** (`withdrawBankEmpty = true`)
   - **Behavior:** Don't show copy button, display "—" only
   - **Reason:** No data to copy

2. **Deposit transactions** (`type = 'deposit'`)
   - **Behavior:** Don't show copy button
   - **Reason:** Feature is for withdrawals only

3. **Missing data attributes**
   - **Behavior:** Copy "undefined" values
   - **Prevention:** Blade template ensures all attributes are set

4. **Special characters in data**
   - **Behavior:** Copy as-is (no escaping needed for plain text)
   - **Example:** Account name with Thai characters works correctly

### Error Handling

1. **Clipboard API not available**
   - **Cause:** Non-HTTPS context (shouldn't happen on localhost)
   - **Handling:** Catch promise rejection, show error toast
   - **User message:** "ไม่สามารถคัดลอกได้"

2. **Permission denied**
   - **Cause:** Browser blocks clipboard access
   - **Handling:** Same as above
   - **User message:** "ไม่สามารถคัดลอกได้"

3. **JavaScript error**
   - **Cause:** Missing data attributes or DOM issues
   - **Handling:** Console.error logs the issue
   - **User impact:** Error toast shown

## Testing Checklist

### Manual Testing

- [ ] Copy button appears only on withdrawal rows
- [ ] Copy button hidden when withdrawal data is empty
- [ ] Copy button not shown on deposit rows
- [ ] Clicking copy button copies correct format
- [ ] Toast notification appears on successful copy
- [ ] Hover effect works (gray → gold)
- [ ] Copied text can be pasted into chat apps (LINE, Telegram, etc.)
- [ ] Works on pending withdrawals (status = 1)
- [ ] Works on approved withdrawals (status = 2)
- [ ] Works on rejected withdrawals (status = 3)
- [ ] Works on in-progress withdrawals (status = 4)
- [ ] Amount formatting includes 2 decimal places
- [ ] Thai characters copy correctly

### Browser Testing

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Regression Testing

- [ ] Existing approve/reject buttons still work
- [ ] Table sorting still works
- [ ] Table search still works
- [ ] Table pagination still works
- [ ] Bank icons still display correctly
- [ ] Responsive layout not broken

## Security Considerations

1. **XSS Prevention**
   - Data attributes use Blade's `{{ }}` syntax (auto-escaped)
   - No user input directly rendered in JavaScript
   - Plain text copy (no HTML injection risk)

2. **Data Exposure**
   - Copy button only shows data already visible on screen
   - No additional sensitive data exposed
   - Follows existing permission model (user must have transfer view permission)

## Future Enhancements

1. **Copy button for deposits**
   - Copy deposit source account details
   - Different format for deposit vs withdrawal

2. **Customizable format**
   - Admin setting to choose copy format
   - Support multiple languages

3. **Copy history**
   - Track which admin copied which withdrawal
   - Audit log for compliance

4. **Batch copy**
   - Select multiple withdrawals
   - Copy all as formatted list

## Dependencies

- **Existing:** SweetAlert2 (already in project)
- **Existing:** Boxicons (already in project)
- **Existing:** Bootstrap 4 (for flexbox utilities)
- **Browser API:** Clipboard API (native, no library needed)

## Rollout Plan

1. **Development:** Implement changes in `transaction/list.blade.php`
2. **Testing:** Manual testing on local environment (`127.0.0.1:8098`)
3. **Review:** Code review and QA approval
4. **Deployment:** Deploy to production
5. **Monitoring:** Check for JavaScript errors in browser console

## Success Metrics

- Copy button visible on all withdrawal rows
- Zero JavaScript errors in console
- Positive feedback from admin users
- Reduced time to process withdrawal requests
