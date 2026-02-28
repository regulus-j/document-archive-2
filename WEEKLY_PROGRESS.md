# Weekly Commit Progress — `finalize-system-functions` Branch

> Branch: **finalize-system-functions** | Total commits recorded: **319** | Total active weeks: **29**

---

## 2026-W09 &nbsp;·&nbsp; Feb 23 – Mar 01, 2026 &nbsp;·&nbsp; 3 commits

### Fri, Feb 27
- feat: Add functionality to create a new workflow for recalled documents

### Thu, Feb 26
- feat: Enhance document upload functionality and UI improvements

### Tue, Feb 24
- feat: add tab navigation for active and archived documents with counts and classification options

## 2026-W08 &nbsp;·&nbsp; Feb 16 – Feb 22, 2026 &nbsp;·&nbsp; 16 commits

### Sat, Feb 21
- fix: update document queries to scope by uploader for accurate company analytics
- fix: update user ID retrieval to use correct relationship for company employees
- fix: scope report queries to current user's company for accurate analytics
- fix: update document restore route to use POST for CSRF protection and correct subscription route duplication
- fix: add 'classification' field to mass assignment for proper access control
- fix: implement validation rules and authorization for document upload requests
- fix: update documentation in Roles middleware to clarify its purpose and usage
- fix: ensure password hashing consistency and improve validation for user creation and updates
- feat: enhance document handling and access control, improve validation, and clean up code

### Fri, Feb 20
- feat: add company branding features including logo upload and color theme selection
- feat: add DocBot AI Chatbot widget and backend support
- Add workflow recipients and responses section to document view
- Set document's originating office to uploader's office if missing during forwarding
- Enhance document review page with dynamic action buttons and status banners
- Enhance Office and CompanyAccount models with transaction handling and subscription checks

### Wed, Feb 18
- feat//viewing and signing docs

## 2026-W07 &nbsp;·&nbsp; Feb 09 – Feb 15, 2026 &nbsp;·&nbsp; 5 commits

### Fri, Feb 13
- Add new report generation and dashboard routes
- Refactor role management to support company-specific roles and permissions

### Wed, Feb 11
- werwre
- compose yml
- Update purpose selection UI with radio buttons

## 2026-W06 &nbsp;·&nbsp; Feb 02 – Feb 08, 2026 &nbsp;·&nbsp; 9 commits

### Sun, Feb 08
- Enhance profile settings
- Fix pricing/plans ui in homepage
- Enhance plans UI with improved layout and design
- Add success messages for password forms

### Fri, Feb 06
- feat: Add advanced filtering options for document index view
- feat: Implement AJAX endpoints for inline category management and enhance document category selection
- feat: Add Document Categories Management
- refactor pricing section to use dynamic plans data and improve billing cycle handling

### Tue, Feb 03
- Trigger contributor cache refresh

## 2026-W05 &nbsp;·&nbsp; Jan 26 – Feb 01, 2026 &nbsp;·&nbsp; 8 commits

### Fri, Jan 30
- Merge branch 'finalize-system-functions'
- fix buttons
- Merge branch 'finalize-system-functions'
- fix buttons
- Hide super-admin role from company-admin users

### Thu, Jan 29
- fix: company CRUD access logic based on user role now works

### Wed, Jan 28
- feat: Update company index and creation flow for improved user experience
- feat: Establish company-user relationship upon company creation

## 2026-W04 &nbsp;·&nbsp; Jan 19 – Jan 25, 2026 &nbsp;·&nbsp; 8 commits

### Tue, Jan 20
- feat: Add Docker entrypoint script to automate migrations, seeding, cache clearing, and Supervisor startup
- bootstrap/app change

### Mon, Jan 19
- added captcha keys to config
- switch docker compose to production->debug
- migrate fix
- Dockerfile fix
- Dockerfile fix
- Dockerfile setup

## 2025-W37 &nbsp;·&nbsp; Sep 08 – Sep 14, 2025 &nbsp;·&nbsp; 14 commits

### Fri, Sep 12
- add:: added search feature in workflow
- fix: workflow table not displaying document recipients
- fix: team assigning issue
- Improve: improve UX for adding users and teams
- Fix: registration and Team feature
- fix: archiving issues
- fix & feature: fix admin dashboard metrics and added completed page

### Thu, Sep 11
- fix & enhancement: Document creation and workflow
- fix: Registration issues

### Wed, Sep 10
- access-control-for-offices-implemented
- sequential-processing-done
- fix-reordering-of-step-order

### Tue, Sep 09
- implemented-half-baked-sequential-processing
- added-ui-for-sequential-workflow

## 2025-W36 &nbsp;·&nbsp; Sep 01 – Sep 07, 2025 &nbsp;·&nbsp; 7 commits

### Fri, Sep 05
- UI: minor improvements on document management
- UI: Fix status filter issue
- UI: added additional status badge
- UI/UX FIX: create and edit document form, document details

### Wed, Sep 03
- UI: enhanced UI/UX for forms and view pages

### Mon, Sep 01
- feature & fix: Added pending document action page & improve document flow
- UI: Enhance document list filtering and status badge interactivity

## 2025-W35 &nbsp;·&nbsp; Aug 25 – Aug 31, 2025 &nbsp;·&nbsp; 11 commits

### Sun, Aug 31
- Fix & UI: Fix issues in archive, enhance UI for receive, document details, and workflow
- UI: enhance create and edit forms
- UI: Enhance Document Management UI/UX
- UI Enhancement: Standardize Design System and Improve User Experience

### Fri, Aug 29
- UI: Complete System Redesign with Standardized Components

### Thu, Aug 28
- UI: Standardize dashboard designs and improve visual consistency
- fix: resolve seeder conflicts and ensure proper user-company assignments
- re organize the files

### Mon, Aug 25
- added-fixes-on-main-process-fixed-recall-method
- added-fixes-on-purpose-functionality-with-proper-permission
- added-fix-on-attachment-file

## 2025-W34 &nbsp;·&nbsp; Aug 18 – Aug 24, 2025 &nbsp;·&nbsp; 4 commits

### Sun, Aug 24
- added fixes on edit and user to user file sending

### Sat, Aug 23
- added-documentation-for-my-changes-on-the-system
- added revisions on receive, document-list, workflow also added a permission control on workflow

### Fri, Aug 22
- fixed some issues on sending file

## 2025-W33 &nbsp;·&nbsp; Aug 11 – Aug 17, 2025 &nbsp;·&nbsp; 10 commits

### Wed, Aug 13
- attachment issue fixed

### Tue, Aug 12
- Merge branch 'Test-branch-ReportsUpdate' into jacobs-test-branch-reportsUpdate
- added-file-attachment-size
- Document tracker: improve ui by adding colors on badges according to actions performed
- Fix archive and workflow module issues

### Mon, Aug 11
- Document details: Hide audit logs to prevent redundancy
- Document Tracker: Improve UX by implementing collapsible section
- Document tracker: minor rephrasing on actions made to the document
- fix document tracker progress indicator not updating
- added: base document progress tracker and fix unable to submit docs

## 2025-W20 &nbsp;·&nbsp; May 12 – May 18, 2025 &nbsp;·&nbsp; 37 commits

### Tue, May 13
- Enforce HTTPS scheme in production environment

### Mon, May 12
- EWAN
- Update User.php
- Update web.php
- Update AuthServiceProvider.php
- Update AppServiceProvider.php
- Update CompanyAccount.php
- Update CompanyController.php
- company to organization name
- Update company account
- returned to older commit - database-controller issues
- fix user create banner
- Merge branch 'ReportsUpdate'
- Update doc review + doc edit page
- fix(subscription): update cancellation logic to disable auto-renewal
- Merge branch 'ReportsUpdate'
- fix: redirect on office delete
- fix: teams limit
- feat: implement upgrade plan
- feat(subscription): show proper upgrade
- Update navigation layout with user dropdown and company admin info
- fix: Clean up formatting, indentation, and red borders in registration form
- feat: Add purpose, urgency, and due date fields to document workflows with validation and UI updates
- create user: update maximum handling
- remove unused
- feat: handle subscription blocking for team creation
- feat(users): include user limit and add user button visibility in user search results
- Merge branch 'ReportsUpdate'
- feat(users): implement user limit display and conditional add user button
- feat(company account): add user limit functionality with fallback for subscriptions
- Added forward document button to docs index. Changed default value of status to uploaded, added new enum values
- feat: Enhance Document Management System
- feat(seeders): update FeatureSeeder and PlanSeeder to include new user, team, and storage limits
- refactor(create user): enhance user creation form layout and add search functionality for roles
- fix(create user): update email validation to allow soft-deleted users and remove unique constraint
- add free tier
- Merge branch 'ReportsUpdate'

## 2025-W19 &nbsp;·&nbsp; May 05 – May 11, 2025 &nbsp;·&nbsp; 30 commits

### Sun, May 11
- Update document creation page
- feat: Implement email verification with verification code
- Deletion Schedule Remove
- Update user navigation, track and receive added
- fix user navigation track and receive
- Added Archived Document Management in the company admin
- Fix role and Improve Filterings
- Fix And Improve User Management
- Merge branch 'ReportsUpdate'
- Change office term to Team
- Change office term to Team
- Change office term to Team
- Change office term to Team
- Change office term to Team

### Sat, May 10
- Change office term to Team
- Change office term to Team
- Change office term to Team
- Update office term to Team
- update sign up

### Fri, May 09
- Removed 'remarks' column from documents table and updated seeder

### Thu, May 08
- fix incorrect routes
- fetch categories from the database
- update seeder to include categories
- fix email sending
- reports: move analytics to index
- fix status null

### Wed, May 07
- fix welcome back
- fix welcome back
- Update welcome page 'Get Started For Free'
- Fix Roles permission superadmin and company-admin

## 2025-W17 &nbsp;·&nbsp; Apr 21 – Apr 27, 2025 &nbsp;·&nbsp; 21 commits

### Thu, Apr 24
- UI changes

### Wed, Apr 23
- feat: Implement Mailgun integration for email handling and queueing
- asdsd
- feat: Enhance user creation and update logic to manage company associations and roles more effectively
- feat: Enhance database seeding for admin and regular users with office assignments
- feat: Implement feature-based plans and subscription management
- feat: enhance user filtering functionality with role selection and search input
- fix: comment out user manual links in navigation for cleaner UI
- fix: update navigation links to ensure proper access based on user roles and subscription status
- feat: Add office lead functionality and dashboard

### Tue, Apr 22
- feat: implement company ownership checks and improve navigation for company accounts
- fix: update company retrieval logic to check for user associations
- sdsdf
- fix: ensure company profile exists before accessing subscriptions
- asdasd
- fix: correct company ID retrieval in active subscription check
- fix: update company ID retrieval in active subscription check
- refactor: comment out company profile check in PlanSelectionController
- fix: correct redirect route for company profile setup in PlanSelectionController
- Add company dashboard PDF report and enhance report generation options

### Mon, Apr 21
- refactor//add PlanSeeder for database initialization

## 2025-W16 &nbsp;·&nbsp; Apr 14 – Apr 20, 2025 &nbsp;·&nbsp; 8 commits

### Sun, Apr 20
- refactor//improve error handling and logging in trial start process
- refactor//enhance document forwarding to include QR code data and improve company office creation logic
- wird git
- Merge branch 'main'
- refactor//remove CHECK constraint on recipient_id and recipient_office in document_workflows migration
- Images changed - Designed Forward.blade

### Fri, Apr 18
- refactor//update middle_name validation to make it optional in user creation
- feat//enhance document workflow and recipient handling in controllers and views

## 2025-W15 &nbsp;·&nbsp; Apr 07 – Apr 13, 2025 &nbsp;·&nbsp; 11 commits

### Sat, Apr 12
- feat//add registration link to login page for new users

### Fri, Apr 11
- feat//assign admin and regular users to random offices and ensure company association
- refactor//update seeders to improve user and company creation logic
- seeder hotfix
- refactor//update OfficeCompanyUser seeder to create company accounts and assign users to offices
- fix//changed doc workflow table received at col to nullable and recipient batch (for user) to be not required
- Merge branch 'main'

### Wed, Apr 09
- Update create.blade.php
- Update create.blade.php
- Update dashboard-office-user view with company name display and optional handling
- Add DocumentCategories seeder and update migration script

## 2025-W14 &nbsp;·&nbsp; Mar 31 – Apr 06, 2025 &nbsp;·&nbsp; 4 commits

### Sun, Apr 06
- Originating and recipient office fix!
- classification fix

### Tue, Apr 01
- Merge branch 'main'
- barebones implementation user office forwarding

## 2025-W13 &nbsp;·&nbsp; Mar 24 – Mar 30, 2025 &nbsp;·&nbsp; 9 commits

### Wed, Mar 26
- fix users nav for super-admin and company-admin

### Tue, Mar 25
- users.index ui
- admin.users-index
- navigation updates
- update navigation
- Updated companies-index.php

### Mon, Mar 24
- minor fix//idk
- minor add//shell script for linux for automigrate
- bugfix//superadmin, company-admin, user routes and seeders

## 2025-W12 &nbsp;·&nbsp; Mar 17 – Mar 23, 2025 &nbsp;·&nbsp; 39 commits

### Thu, Mar 20
- Refactor workflow management view with improved layout and styling
- Rename 'Admin' role to 'super-admin' in seeder and update dashboard view
- Add admin role assignment on user registration, update payment method length

### Wed, Mar 19
- Fix redirect to view in DashboardController and conditionally display Free Trial button for admins
- Implement UI updates, enhance user role management, and add subscription renewal command
- Ui Update
- Ui Update
- Ui Update
- Ui updated
- Ui updated
- Ui updated
- Ui updated
- Another Ui Changes
- Another Ui Changes
- Another Ui Changes
- Ui updated

### Tue, Mar 18
- fixing document archive & form -undone
- fix uploading documents (not done)

### Mon, Mar 17
- Merge branch 'main'
- Ui changes
- Add plan management features: implement show, edit, and update functionality in PlanController
- Merge branch 'main'
- Add factories for Office and CompanyAccount models; implement plan creation logic in PlanController
- Merge branch 'main'
- Document View Ui Update
- Update dashboard to include counts for pending and recent documents
- Hindi ko maayos ang cms...
- Landpage Fix/ Change Pic later
- manual
- Dashboard
- User Manual
- User Manual
- Merge branch 'main'
- User Manual
- Merge branch 'main'
- Add separate dashboard views for admin and office users; update DashboardController
- Updated Admin UI
- Fix URL
- Fix trial logic and database query for company_users

## 2025-W11 &nbsp;·&nbsp; Mar 10 – Mar 16, 2025 &nbsp;·&nbsp; 4 commits

### Sun, Mar 16
- Login/Register UI Updated
- minorfix

### Sat, Mar 15
- Refactor DocumentWorkflowController to log document actions using document IDs

### Thu, Mar 13
- Merge branch 'main'

## 2025-W08 &nbsp;·&nbsp; Feb 17 – Feb 23, 2025 &nbsp;·&nbsp; 19 commits

### Fri, Feb 21
- Minor Refactor//Document & Doc Workflow Controller

### Thu, Feb 20
- Add admin views for managing users and subscription plans; update Plan and Subscription controllers for admin access

### Wed, Feb 19
- Debugging
- Merge remote-tracking branch 'origin/BusinessSubscrip'
- (empty message)
- Add company association to Office and User models; create CompanyUser model
- updated landing page
- No changes
- Plans and Payment
- Feature/mock-paymongo links payment

### Tue, Feb 18
- Implement admin dashboard and user management features
- Refactor/business-side company logic implemented
- Merge branch 'DocumentRoutingModule'
- Merge remote-tracking branch 'origin/BusinessSubscrip' into DocumentRoutingModule
- Merge branch 'DocumentRoutingModule'
- Merge pull request #4 from regulus-j/DocumentRoutingModule
- Merge remote-tracking branch 'origin/frontend' into DocumentRoutingModule
- change ui
- Features/doc-routing updates, reports and analytics page

## 2025-W07 &nbsp;·&nbsp; Feb 10 – Feb 16, 2025 &nbsp;·&nbsp; 5 commits

### Fri, Feb 14
- Feature/document-forwarding

### Tue, Feb 11
- Refactor User model relationship, enhance document views with optional tracking number, and implement document forwar...
- Reports Update
- BusinessIntegrate
- Feature/Archive UI, Routes

## 2025-W06 &nbsp;·&nbsp; Feb 03 – Feb 09, 2025 &nbsp;·&nbsp; 4 commits

### Fri, Feb 07
- Feature/Document Workflow DB + Backend
- change ui txt

### Thu, Feb 06
- DB+Model//subscriptions, company accounts, notifications

### Wed, Feb 05
- FRONT

## 2025-W04 &nbsp;·&nbsp; Jan 20 – Jan 26, 2025 &nbsp;·&nbsp; 1 commit

### Sat, Jan 25
- New Migrations: please test mwa<3

## 2024-W50 &nbsp;·&nbsp; Dec 09 – Dec 15, 2024 &nbsp;·&nbsp; 1 commit

### Mon, Dec 09
- updates

## 2024-W49 &nbsp;·&nbsp; Dec 02 – Dec 08, 2024 &nbsp;·&nbsp; 14 commits

### Sat, Dec 07
- Merge branch 'ocrProcessing' into frontend
- Merge remote-tracking branch 'origin/ocrProcessing' into frontend
- ewan
- Refactor Profile and User controllers for improved code clarity and consistency
- Enhance dashboard with document statistics and improve layout
- Add Poppler dependency and refactor models for document management

### Fri, Dec 06
- Refactor project structure and improve user interface components
- Add lucide-react dependency and update role details view for improved layout

### Thu, Dec 05
- Add document attachments feature and update related components
- Merge pull request #2 from regulus-j/frontend

### Wed, Dec 04
- frontend UI

### Mon, Dec 02
- Merge branch 'ocrProcessing'
- create, edit, audit docs, offices crud
- create, edit, audit docs, offices crud

## 2024-W48 &nbsp;·&nbsp; Nov 25 – Dec 01, 2024 &nbsp;·&nbsp; 5 commits

### Sun, Dec 01
- Update

### Fri, Nov 29
- fixed stupid bug
- Merge pull request #1 from regulus-j/ocrProcessing
- basta
- Ambot, gi butangan search sa users

## 2024-W47 &nbsp;·&nbsp; Nov 18 – Nov 24, 2024 &nbsp;·&nbsp; 4 commits

### Sat, Nov 23
- UKINAM
- Add Backup and Team management features with initial setup

### Thu, Nov 21
- Added NLP-based search + Functioning Camera

### Wed, Nov 20
- Di nako kahinomdom, basta gagana na upload for docx and images, pdf work in progress pa

## 2024-W46 &nbsp;·&nbsp; Nov 11 – Nov 17, 2024 &nbsp;·&nbsp; 8 commits

### Sun, Nov 17
- Working OCR, other file types not tested
- barebones image uploading only (ocr not working as intended)
- not done
- RBAC Backend

### Thu, Nov 14
- Added email config + user invite module
- IDK

### Wed, Nov 13
- Install Breeze
- Set up a fresh Laravel app
