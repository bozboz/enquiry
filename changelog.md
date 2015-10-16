# Version 1.1.0 (16/10/2015)

-	Subject and views can be passed to Enquiry::make()
-	send() method can be called prior to make, to specifiy recipient
-	Update PSR-0 to PSR-4
-	Add `withSubject` method to `Process`


# Version 1.0.0 (15/01/2014)

-	Bump Illuminate dependencies to 4.2
-	Add readme
-	Change config values (contact_recipient => enquiry_recipient_address, contact_name => enquiry_recipient_name)
-	Change `validator` method to more appropriate `getErrors` method
-	Add docblocks to Process class
