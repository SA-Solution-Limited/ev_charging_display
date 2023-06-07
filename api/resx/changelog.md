# Technetium PHP Framework - Change Log



## Version 3.x

*Released: MMM DD, YYYY*

### Feature Update - Framework:
- Add setup guide (TBI)
- Add Shopping Cart module
- Add VISA, MasterCard and PayPal payment module

### Feature Update - Framework:
- Localization

### Feature Update - Administration Panel:
- Add Google Adsence module
- Support post tagging (TBI)

### Development Tool Updates:
- Repackage all tools
- Support light and dark themes

### Setup Wizard Updates:
- Revamp user interface
- Update instructions



## Version 2.9

*Released: APR 13, 2023*

### Front-end Updates:
- Add *functions.php* for custom template functions
- Add logos of common payment methods
- Repackage single-page application framework as `Spa`
- Revamp the user interface
- Update Google Analytics tracking code to support both *analytics.js* and *gtag.js*
- Unify the use of *https://cdn.cruzium.info* for CSS and JS libraries

### Administrator Panel Updates:
- Add class `AdminController` and deprecate classes `AdminIndex` and `AdminForm` in future release
- Adjust settings of TinyMCE
- Add modules *Post* and *Term*
- Implement new password storage logic
- Coding rewrite and minor layout adjustments

### Generic Coding Updates:
- Auto parse JSON request body for `POST`, `PUT` and `DELETE` requests
- Deprecate the following classes and relocate the methods to corresponding helper class
  - Class `TemplateEngine`
  - Class `Util`
- Rename template files to better classify whether it is a page or a components
  - *page-footer.php* => *com.footer.php*
  - *page-header.php* => *com.header.php*
  - *page-meta.php* => *com.meta.php*
  - *page-404.php* => *page.404.php*
  - *page-500.php* => *page.500.php*
- Update class `Db`
  - Support auto-insert/replace/update of encrypted table columns
- Update class `FormUpdate`
  - Support file grouping
- Update class `Notification`
  - Allow customizing parameter delimiters
  - Support logging to designated file
- Update class `Proxy`
  - Add user agend declaration
  - Handle 30x responses
  - Skip SSL host verification
  - Support basic auth

### Database Updates:
- Update table `resx_country`
  - Add missing data
  - Add European Union
  - Add column `ccySymbol` (currency symbol)

### Development Tool Updates:
- Add *HTML Exporter*
- Add *Iframe Wrapper*
- Refurnish *Database Entity Generator*
- Refurnish *Email Blaster*

### Miscellaneous:
- Add cron job scheduler
- Introduct *composer* for dependency management
- Update sitemap.xml generation script
- Update ownership declaration placed at the top of files to comply with phpDocumentor standard



## Version 2.8

*Released: MMM DD, YYYY*

### Feature Updates - Framework:
- Accept dynamic and database routing
- Re-allocate PHP class and config files
- Isolate environment config and site config
- Utilities updates:
  - Accept new TLDs for email validation
  - Add punctuation excludsion validation
- Add "doNotTrack" config option

### Feature Updates - Administration Panel:
- Add built-in module: Media, Post
- Add sorting to table views
- Add session timeout detection
- Allow disabling auto-authentication ("Remember Me")

### Bug Fixing:
- Admin panel detection



## Version 2.5

*Released: SEP 16, 2014*

### Feature Update - Framework:
- Add Shorten URL module
- Use database as session handler if database connection is detected

### Layout Update - Administrator Panel:
- Support customized themes

### Feature Update - Administration Panel:
- Add forgot password module
- Implement TinyMCE as default rich-text editor
- Support content localization
- Support database record filtering 
- Support sub-menu item (up to level 3) and recursive folder structure



## Version 2.3

*Released: DEC 13, 2013*

### Feature Update - Framework:
- Add default locale file for localization inheritance
- Add SMTP configuration file

### Feature Update - Administration Panel:
- Account activation required upon user creation
- Add comfirmation upon data deletion
- Add data table as a form control
- Add Google Analytics module to Dashboard
- Automate password reset procedures
- Support AJAX media upload

### Coding Update:
- Handle CSS and JS embedment by PHP function
- Replace deprecated MySQL extension by PDO database extension
- Standardize name attribute of checkbox inputs (changed from "name"
  to "name[]")

### Database Update:
- Add column to user table to store user's email
- Encrypt and mirgrate user data with AES algorithm

### Bug Fixing:
- SQL query bug
- User Manager display issues
- Users are able to disable his own account throught bulk action


Version 2.2.5

Released: JUL 31, 2013

### Feature Update - Framework:
- Support backend-only systems

### Feature Update - Administration Panel:
- Support auto-login

### Coding Update:
- Support new MIME type (apk, avi, bmp, eot, exe, h263, h264, mpeg,
  ogv, otf, ppsx, rtf, wav, weba, wma, woff)

### Bug Fixing:
- Login session shared under same domain



## Version 2.2

*Released: JUN 30, 2013*

### Feature Update - Framework:
- Add customized HTTP 404 error page
- Auto-redirect on resources folder requests
- Remove fallback functions for class Util
- Support multi-stage database setting

### Feature Update - Administration Panel:
- Add banner module
- Add media module
- Add password strength meter
- Modulize form items
- Security enhancement on login failure
- Support 2nd level sub-category
- Support no script environment
- Support scheduled publishing
- Support user role and privileges

### Coding Update:
- Implement object-oriented programming

### Miscellaneous:
- Add humans.txt (humanstxt.org)
- Build-in reCAPTCHA library
