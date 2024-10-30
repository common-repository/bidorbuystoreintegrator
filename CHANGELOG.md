# WooCommerce bidorbuy Store Integrator

### Changelog

#### 2.12.0

* New Feature to valide the bidorbuy tradefeed to make sure it has no errors.

[Updated on May 06, 2021]

#### 2.10.0

* Technical release.

[Updated on April 16, 2021]
#### 2.9.0

* Technical release.

[Updated on April 16, 2021]

#### 2.8.0

* Updated to Composer 2

[Updated on April 16, 2021]

#### 2.7.14

* Technical release.

[Updated on April 15, 2021]
#### 2.7.13

* Added compatibility with PHP 7.3

[Updated on February 7, 2020]

#### 2.7.12

* Support BOBSI-6 IOException: Premature EOF

[Updated on January 22, 2020]

#### 2.7.11

* Technical release.

[Updated on January 21, 2020]

#### 2.7.10

* Defect #7031: [Bidorbuy - WooCommerce] Fix plugin version issue

[Updated on October 10, 2019]

#### 2.7.9

* Technical release.

[Updated on October 10, 2019]

#### 2.7.8

* Technical release.

[Updated on October 8, 2019]

#### 2.7.7

 * Readme file - adding corporate URL and minor updates.

_[Updated on September 17, 2019]_

#### 2.7.6

 * Defect #6769: Fix warning for users in case if WooCommerce is deactivated.

_[Updated on July 09, 2019]_

#### 2.7.3

 * Defect #6738: Don't export products without title.

_[Updated on June 12, 2019]_

#### 2.7.2

 * Support #6557: WooCommerce bidorbuy Store Integrator | Error: 404

_[Updated on April 23, 2019]_


#### 2.7.1

 * Support #6451: WooCommerce bidorbuy Store Integrator | Error: IOException: Premature EOF

_[Updated on April 17, 2019]_

#### 2.7.0

 * Feature #6430: Support apache 2.2 and apache 2.4 without errors.

_[Updated on March 28, 2019]_

#### 2.6.7

 * Support #6389: WooCommerce Store Integrator | plugin conflict between the BidorBuy plugin and the Advanced Bulk Edit.

_[Updated on March 19, 2019]_


#### 2.6.6

 * Defect #6209: WooCommerce StoreIntegrator | Uncaught TypeError: Argument 1 passed to BidorbuyStoreIntegratorExport::getProductId() must be an instance of WC_Product, boolean given.

_[Updated on January 28, 2019]_
 
 #### 2.6.5

 * Defect #6170: Store Integrator Core | a code`s typo.
 * EOL (End-of-life due to the end of life of this version) for PHP 5.5, 5.6, 7.0 support.

_[Updated on January 14, 2019]_ 

#### 2.6.4

 * Added warning if Comet Cache is enabled and URI exclusion was not added (Request #6127).

_[Updated on December 24, 2018]_ 

#### 2.6.3

 * Fixed an issue when tradefeed is pulling through the code to the product description tag, in case of using shortcodes (Support #6078).

_[Updated on November 25, 2018]_ 
 
#### 2.6.2

 * Fixed warning on php7.1: "A non well formed numeric value encountered".
 * Fixed save settings defect: Duplicated condition.
 
 _[Updated on November 20, 2018]_ 
 
#### 2.6.1

* Fixed UI conflict with Rocket Dashboard and download issue caused by WooCommerce Product Feed plugins (supports #5588 and #5579).
* EOL of WordPress 4.7.x support (31 August 2018).

_[Updated on Aug 18, 2018]_

#### 2.6.0

* Added possibility to change product condition in XML (available conditions: New, Secondhand, Refurbished).

_[Updated on June 14, 2018]_

#### 2.5.1

* Fixed export issue - when admin flies were extracted to a separate folder.
* EOL (End-of-life due to the end of life of this version) for PHP 5.4 support.
* EOL (End-of-life due to the end of life of this version) for WordPress 4.3.x, WordPress 4.4.x, WordPress 4.5.3.x.
 
_[Updated on May 31, 2018]_

#### 2.5.0

* XML:Product Code changes| export only assigned SKU (product ID is ommitted).

_[Updated on May 10, 2018]_

#### 2.4.2

* Fixed issue when newly created category was included into Allow Offer tab.

_[Updated on May 03, 2018]_

#### 2.4.1

* Changed default behaviour for Allow Offer feature - false by default.

_[Updated on May 01, 2018]_

#### 2.4.0

* Added Allow Offer feature.

_[Updated on April 19, 2018]_

#### 2.3.1

* Fixed core export error: could not resolve host...

_[Updated on April 13, 2018]_

#### 2.3.0

* XML: Added GTIN tag.
* XML: Added feature to fetch images from short product description.
* Setting page improvements: renaming Debug sections.

_[Updated on April 13, 2018]_

#### 2.2.2

* Added workaround with checking permalink_structure and REQUEST_URI for `Custom Permalink Structure broke Store Integrator activity` issue.

_[Updated on March 15, 2018]_

#### 2.2.1

* Added support for WooCommerce 3.3.3: added uncategorized category, fixed tooltips.

_[Updated on March 06, 2018]_

#### 2.2.0

* Logging error improvements: added logging event and disabling log options.

_[Updated on February 14, 2018]_

#### 2.1.6

* Fixed issue when description was not properly exported.

_[Updated on February 03, 2018]_

#### 2.1.5

* Fixed error when product with q-ty=0 is exported to feed.

_[Updated on February 01, 2018]_

#### 2.1.4

* Fixed error related to PSR-2 coding standards.

_[Updated on January 29, 2018]_

#### 2.1.3

* Added HTTPS to the AdVert Pro URL on Store Integrator Settings page.
* Changed plugin code according to PSR-2 coding standards.
* Removed 'Send Logs To Email' feature.

_[Updated on January 26, 2018]_

#### 2.1.2

* Improved the description export process.

_[Updated on December 06, 2017]_

#### 2.1.1

* Added force CONVERT TO CHARACTER SET utf8_unicode_ci by default for next database tables: bobsi_tradefeed_product, bobsi_tradefeed_product_base, bobsi_tradefeed_audit.

_[Updated on November 30, 2017]_

#### 2.1.0

* Restored compatibility with WooCommerce < 3.0.0.
* Fixed dependence on pretty permalinks.

_[Updated on November 17, 2017]_

#### 2.0.15

* Adopted plugin according to wordpress.org rules.

_[Updated on November 14, 2017]_

#### 2.0.14

* Reporting each Reset Audit effort as an extra notification in a log file (only for Debug logging level).

_[Updated on November 07, 2017]_

#### 2.0.13

* Added possibility to include the attributes in product titles for the WooCommerce integrator.
* Fixed an issue when export throws fatal error in case if WooCommerce is deactivated.
* Fixed an issue when WooCommerce throws warning in case of deleting the attributes.
* Fixed an issue when `Draft` and `Pending` products are missing in feed.
* Corrected headers processing in Store Integrator core.

_[Updated on September 30, 2017]_

#### 2.0.12

* Improved the logging strategy for Debug level.
* Added extra save button which was removed from Debug section (the settings page).

_[Updated on August 21, 2017]_

### 2.0.11

* EOL (End-of-life due to the end of life of this version) for PHP 5.3 support.
* Added support for WooCommerce 3.1.1.

_[Updated on August 02, 2017]_

### 2.0.10

* Fixed error in query (1292): Incorrect datetime value: '0000-00-00 00:00:00' for column 'row_modified_on' at row 1.
* Fixed error in query (1055): Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column.
* Fixed issue when "$this->dbLink->execute" hides the real error messages.
* Fixed issue when bobsi tables are created always with random charset instead of utf8_unicode_ci.
* Fixed issue when export process is interrupted by zlib extension.

_[Updated on June 06, 2017]_

### 2.0.9

* Added a flag to display BAA fields (to display BAA fields on the setting page add '&baa=1' to URL in address bar).
* Added an appropriate warning on the Store Integrator setting page about EOL(End-of-life) of export non HTTP URL to the tradefeed file.

_[Updated on March 07, 2017]_

### 2.0.8

* Improved the upgrade process.

_[Updated on December 29, 2016]_

### 2.0.7

* Added support of multiple images.
* Added support of images from product description.
* Added the possibility to open PHP info from store Integrator settings page.

_[Updated on December 20, 2016]_

### 2.0.6

* Added additional improvements for Store Integrator Settings page.
* Fixed an issue when Store Integrator cuts the long name of categories in Export Criteria section.

_[Updated on November 18, 2016]_

### 2.0.5

* Added new feature: if product has weight attribute, the product name should contain this attribute value.
* Fixed an issue when tradefeed is invalid to being parsed with Invalid byte 1 of 1-byte UTF-8 sequence.

_[Updated on November 02, 2016]_

### 2.0.4

* Fixed an issue of empty XML after changing the settings.
* Fixed an issue when it is impossible to download log after its removal.
* Fixed an issue when extra character & added to the export URL.
* Corrected the export link length: it was too long.
* Added an error message if "mysqli" extension is not loaded.

_[Updated on October 18, 2016]_

### 2.0.3

* Added warning in case if 'readfile' function is disabled.
* The PHP version has changed to 5.3.0.
* Fixed Settings Page styles.

_[Updated on August 19, 2016]_

### 2.0.2

* Added support for WordPress 4.5.x (WooCommerce 2.6.x and WooCommerce 2.5.x).

_[Updated on July 07, 2016]_

### 2.0.1

* Added Reset export data link to a plugin settings page.
* Added a possibility to check the plugin version.
* Fixed a bug when on certain occasions disabled products were still exported.

_[Updated on April 27, 2016]_

### 2.0.0

* Added optimization technology for huge data sets, which significantly improves integrator performance.
* Enhancements and bugs fixes.

_[Updated on September 12, 2015]_

### 1.0

* First release.
 
_[Released on April 07, 2014]_