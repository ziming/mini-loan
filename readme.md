### Controllers, URI Design
For controllers & URI design I generally try to follow the 4 guidelines in Adam Wathan's Cruddy By Design Talk in Laracon 2017

https://github.com/adamwathan/laracon2017/pull/1

https://www.youtube.com/watch?v=MF0jFKvS4SI

https://github.com/adamwathan/laracon2017/blob/master/routes/web.php

I referenced Phil Sturgeon: "Build API You won't hate" a little too but my current understanding of building REST APIs is very weak
like I'm not even sure if I should return a silent 200 or success or with a message.etc. But I suppose there are a few standards 
we can pick & choose to follow out there

https://leanpub.com/build-apis-you-wont-hate

### Routes

See routes/api.php mostly. routes/web.php only contain the Register User route that is produced by `php artisan make:auth`
so that's not done by me

### Currency
I decided to have them as decimal in the database and use Eloquent/QueryBuilder increment() and decrement() method 
to add/substract at the database SQL level instead of storing them as cents. 
But thinking back cents seem to be a better choice. Your thoughts?

### TDD, testing
I adopt the London style of TDD (outside-in) mostly instead of the 
Chicago style (inside-out). This seem to be the more common style in the 
Laravel community.

I only do TDD on the core flows to flesh the api out. It's not thorough

I'm not quite sure on the differences between assertJson or assertJsonFragment. I just alternate between them & see what works

I used $this->json('POST', ..., ...) instead of $this->post(...) because of this article that highlights some issues
with the response code differences

https://dyrynda.com.au/blog/testing-json-apis-with-laravel-5

### Creating Users, Authentication

Creating a user is done via the normal way via user registration on the register page. Which is already provided by 

```
php artisan make:auth
```

So I trust it's implementation and did not test it.

However authentication is done via Laravel Passport OAuth2. I never done Passport/OAuth 2 before so it might be very wrong

Any good resources on this would be nice or good code samples would be nice.

See tests/Feature/PassportFeaturesTest.php for the test on the different grants. I did not make use of scopes

This test class take up the most time so you may want to not run it.


### Misc Considerations

- Should LoanContract be split into LoanApplication and Loan? I chose to group them together for simplicity. But I'm not 
sure