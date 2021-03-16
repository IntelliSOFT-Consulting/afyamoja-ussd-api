<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About AfyaMoja

The AfyaMoja USSD and API code base is meant to allow for a client system to able to access patient details either through USSD or API.
The system is connec

The following endpoints / actions are available on the system.

### User Actions
A user is able to register and access the system. 
In the event that they forget they pin, it can be reset it and the new pin will be sent via sms. 
The user can fully delete their account if for one reason or another they want to exit the system.

    login
    registration
    reset_pin
    change_pin
    forget_patient

### Dependent Actions
To allow for easy mangement of dependents linked to the patient

    dependents
    add_dependent
    delete_dependent

### Patient Actions
The patient/user needs to be able to access and share their personal and medical records.

    share_records
    last_visit
    full_history
    patient_profile

More details can be accessed about the API endpoints from the Wiki Section.
For the USSD the logic can be found on the Master Controller


## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Error

After installing on production run

    php artisan key:generate
    php artisan config:cache
    php artisan config:clear
    php artisan cache:clear


To install files for vendor run 

    bash ```
        composer install
    ```

