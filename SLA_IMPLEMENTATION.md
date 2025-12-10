# AIHRA Ticket SLA Management System

## Overview
The AIHRA chatbot includes an automated ticket priority and expiration tracking system based on Service Level Agreement (SLA) timeframes. This ensures timely responses to employee inquiries.

## Priority Levels & SLA Timeframes

### Urgent Priority
- **Response Deadline:** 30 minutes
- **Resolution Deadline:** 4 hours
- **Keywords:** emergency, urgent, critical, asap, immediately, harassment, discrimination, assault, unsafe, danger, threat, suicide, self-harm, violence, abuse

### High Priority
- **Response Deadline:** 1 hour
- **Resolution Deadline:** 8 hours
- **Keywords:** fired, termination, terminated, legal, lawyer, police, bullying, severe, serious complaint, cannot work, injury, accident, medical emergency

### Medium Priority
- **Response Deadline:** 8 hours
- **Resolution Deadline:** 48 hours
- **Keywords:** complaint, issue, problem, concern, dispute, salary issue, pay problem, not paid, wrong pay, disciplinary, warning, promotion denied

### Low Priority
- **Response Deadline:** 24 hours
- **Resolution Deadline:** 72 hours
- **Default priority** for general inquiries and questions

## How It Works

### 1. Ticket Creation
When a ticket is created (escalated from chatbot):
- System analyzes the message content for priority keywords
- Assigns appropriate priority level (Urgent/High/Medium/Low)
- Calculates `response_deadline` and `resolution_deadline` timestamps
- Stores ticket with deadline information in `hr_inbox` table

### 2. Deadline Tracking
The system tracks two types of deadlines:
- **Response Deadline:** When HR must provide first response
- **Resolution Deadline:** When ticket must be fully resolved

### 3. Expiration Checking
Automated checks run every 15 minutes via Laravel scheduler:
```bash
php artisan schedule:run
```

Manual check command:
```bash
php artisan tickets:check-expired
```

Tickets are marked as expired (`is_expired = true`) when:
- Response deadline passed without any HR reply (`responded_at` is null)
- Resolution deadline passed without ticket being resolved (`resolved_at` is null)

### 4. Timestamp Recording
The system automatically records:
- `responded_at`: Set when HR sends first reply
- `resolved_at`: Set when ticket is marked as Resolved
- `is_expired`: Set to true when deadlines are exceeded

## Database Schema

New fields added to `hr_inbox` table:
```sql
response_deadline    TIMESTAMP  -- When first response is due
resolution_deadline  TIMESTAMP  -- When resolution is due
responded_at         TIMESTAMP  -- When HR first responded
resolved_at          TIMESTAMP  -- When ticket was resolved
is_expired          BOOLEAN     -- Whether ticket missed deadline
```

## API Endpoints

### Check Expired Tickets
```
GET /hr/check-expired-tickets
```
Manually triggers expiration check and returns results.

### Get Expiration Statistics
```
GET /hr/expiration-stats
```
Returns statistics about expired and expiring tickets.

## Important Notes

1. **Expired tickets remain active** - They are not automatically closed, just flagged for visibility
2. **Priority is determined automatically** - Based on message content analysis
3. **Confidence-based fallback** - If no keywords match, chatbot confidence score determines priority
4. **Scheduler must be running** - For automatic expiration checks, ensure Laravel scheduler is running:
   ```bash
   php artisan schedule:work
   ```
   Or set up a cron job:
   ```
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Files Modified/Created

### New Files
- `app/Services/TicketExpirationService.php` - Core expiration checking logic
- `app/Console/Commands/CheckExpiredTickets.php` - Manual check command
- `database/migrations/2025_11_27_000001_add_ticket_expiration_fields.php` - Database schema

### Modified Files
- `app/Http/Controllers/DialogflowController.php` - Priority determination and deadline calculation
- `app/Http/Controllers/HRController.php` - Timestamp recording on reply/resolve
- `app/Http/Controllers/AdminController.php` - Added expiration statistics
- `app/Models/HrInbox.php` - Added new fields to fillable and casts
- `app/Console/Kernel.php` - Added scheduled task

## Future Enhancements

Potential improvements to consider:
- Visual indicators in UI for expired tickets (red badges)
- Email notifications for expiring tickets
- Dashboard widgets showing SLA compliance metrics
- Escalation rules for repeatedly expired tickets
- Historical SLA performance reports
