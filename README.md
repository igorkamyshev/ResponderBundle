# ResponderBundle

> A Symfony Bundle to easy handling returning any type from controllers and transforming it to Response

## Setup

To add this package to your composer.json and install it execute the following command:

```
composer require kamyshev/responder-bundle
```

Add ResponderBundle to your application

```php
// config/bundles.php
return [
    // ...
    Kamyshev\ResponderBundle\ResponderBundle::class => ['all' => true],
    // ...
];
```

## Usage

Adding a new responder requires creating a class that implements ResponderInterface and defining a service for it. The interface defines two methods:

+ `supports()` — This method is used to check whether the responder supports the given argument. make() will only be executed when this returns true.
+ `make()` — This method will create the actual response for the argument.

Both methods get the Result object, which was be returned from controller, and an ResultMetadata instance. This object contains all information retrieved from the method signature for the current argument.

Now that you know what to do, you can implement this interface. Return `User` from controller action and:

```php
// src/Responder/UserResponder.php
namespace App\Responder;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Kamyshev\ResponderBundle\Responder\ResponderInterface;
use Kamyshev\ResponderBundle\Responder\ResultMetadata;

class UserResponder implements ResponderInterface
{
    public function supports($user, ResultMetadata $meta): bool
    {
        return User::class === $meta->getType();
    }
    
    public function make($user, ResultMetadata $meta): Response
    {
        return new Response($user->getName());
    }
}
```

That's it! Now all you have to do is add the configuration for the service container. This can be done by tagging the service with `kamyshev.responder`.

```
# config/services.yaml
services:
    _defaults:
        # ... be sure autowiring is enabled
        autowire: true
    # ...

    App\Responder\UserResponder:
        tags: ['kamyshev.responder']
```

All done! You can return User from controller and handle it in Responder.