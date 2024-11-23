# Symfony Console Input Plugin

This project provides a streamlined way to define Symfony Console commands using PHP attributes and Data Transfer Objects (DTOs). It simplifies the configuration of arguments and options by leveraging metadata annotations, making it easier to maintain and extend console commands.

## Features

- Automatically generates command arguments and options from DTO properties.
- Supports metadata annotations (`#[Argument]`, `#[Option]`, etc.) to define command inputs.
- Easily integrates with Symfony's Console component.
- Provides a `UsesInputData` trait to configure and initialize input data automatically.

## Installation

To install the plugin, use Composer:

```bash
composer require baethon/symfony-console-input
```

## Getting Started

### 1. Define Your Input DTO

Create a class with properties annotated using provided attributes like `#[Argument]` or `#[Option]`. 

```php
use Baethon\Symfony\Console\Input\Attributes\Argument;
use Baethon\Symfony\Console\Input\Attributes\Option;

readonly class MyCommandInput
{
    public function __construct(
        #[Argument]
        public string $name,   // A required argument

        #[Option]
        public int $age = 18,  // An optional option with a default value
    ) {}
}
```

> [!IMPORTANT]
> The input class must define all arguments and options in the constructor.

### 2. Create a Command

Use the `UsesInputData` trait in your Symfony Console command class to leverage the input configuration:

```php
use Baethon\Symfony\Console\Input\Attributes\InputData;
use Baethon\Symfony\Console\Input\UsesInputData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'my-command')]
class MyCommand extends Command
{
    use UsesInputData;

    #[InputData]
    private MyCommandInput $input;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Name: {$this->input->name}");
        $output->writeln("Age: {$this->input->age}");

        return Command::SUCCESS;
    }
}
```

## Attributes

### `#[Argument]`

Marks a property as a required/optional argument.

### `#[Option]`

Marks a property as an option.

### `#[Shortcut]`

Defines a shortcut for an option.

### `#[Description]`

Adds a description for the argument or option.

### `#[Name]`

Sets a different name for the argument or option.

## Testing

Run tests using Pest:

```bash
./vendor/bin/pest
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
