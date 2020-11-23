=== pVerify ===
Requires at least: 4.7
Tested up to: 5.5.3
Requires PHP: 7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: later
 
This plugin is designed to be used in conjunction with pVerify for access to our Estimator Widget.
 
== Description ==
 
This plugin is designed to be used in conjunction with pVerify for access to our Estimator Widget. You will need an account with pVerify as a medical provider (with valid NPI) to use this service. Once your account is active, you can use this plugin.

The pVerify Wordpress plugin is meant to enable use of our pVerify widget in Wordpress sites. This widget communicates with pVerify to transmit patient information to check eligibility and return patient cost estimates.

Our website is available at www.pverify.com for further information. Our terms and conditions is here: https://pverify.io/terms/

Use:

You will need to obtain the ID/secret from pVerify and add it to the plugin interface.

Then you can use the plugin shortcode anywhere in Wordpress to expose the pVerify Estimator Widget. This allow you to collect patient name, DOB, member ID, payer name, and type of lab test, and submit insurance verification and calcualte expected patient cost of test. Additionally you can add a email and/or webhook on the pVerify side to capture the outbound data that the user enters to connect to your CRM or other software systems.

Please contact us for further information. support@pverify.com

Thank you
 
== Installation ==
 
1. Upload `pVerify` plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through 'Plugins' menu in WordPress
3. Add Client API ID and Client Secret Key in plugin backend then you will get shortcodes
4. Add Estimate Enquiry Widget shortcode and Eligibility Widget shortcode on page
5. Then we will see Estimate enquiry widget form and Eligibility widget form
 
== Frequently Asked Questions ==
 
= How to use pverify-widget?
To use pVerify Estimator Widget we need to use this shortcode [pverify-widget]
 
= How to use eligibility-widget?
To use pVerify Eligibility Widget we need to use this shortcode [eligibility-widget]
 
== Screenshots ==
 
1. Screenshot_1.png
 
== Changelog ==
 
= 1.0 =
