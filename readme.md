# Bozboz\Enquiry

## Installation

1. Add `Bozboz\Enquiry\EnquiryServiceProvider` to the `providers` array in `app/config/app.php`
2. Add `contact_recipient` and `contact_name` to `app/config/app.php`
3. Add `'Enquiry' => 'Bozboz\Enquiry\Facades\Enquiry'` to the `aliases` array in `app/config/app.php`
4. Set `from` and `name` in `app/config/mail.php`
5. Setup a route

```
    Route::post('contact', array('as' => 'contact-process', function()
    {
        $enquiry = Enquiry::make(Input::get(), array(
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:20'
        ));

        if ($enquiry->fails()) {
            return Redirect::to(URL::previous() . '#form')->withInput()->withErrors($enquiry->validator());
        } else {
            return Redirect::to(URL::previous() . '#form')->with('success', true);
        }
    }));
```

6. Point your contact form to the above route

```
    {{ Form::open(['route' => 'contact-process', 'role' => 'form', 'id' => 'form']) }}
```
7. Create the `enquiry` and `enquiry-text` files within `app/views/emails`
