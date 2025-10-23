# Contributing to Merchandise Sales Plugin

Thank you for considering contributing to the Merchandise Sales Plugin! This document outlines the guidelines for contributing to the project.

## Code of Conduct

By participating in this project, you agree to abide by our code of conduct. Please be respectful and considerate of others.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue tracker to see if the problem has already been reported. If it has, add a comment to the existing issue instead of opening a new one.

When you are creating a bug report, please include as many details as possible:

- Use a clear and descriptive title
- Describe the exact steps to reproduce the problem
- Describe the behavior you observed and what you expected to see
- Include screenshots if possible
- Include details about your environment (WordPress version, PHP version, browser, etc.)
- Use the bug report template if available

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- A clear and descriptive title
- A detailed description of the proposed functionality
- Any possible implementation details
- Why this enhancement would be useful to most users
- Use the feature request template if available

### Pull Requests

- Fill in the required template
- Follow the WordPress Coding Standards
- Include tests for new functionality
- Update documentation for changes
- End all files with a newline
- Make sure your code passes all tests

## Development Workflow

### Setting Up the Development Environment

1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR-USERNAME/merchmanager.git`
3. Install dependencies: `composer install`
4. Set up a local WordPress development environment
5. Activate the plugin in WordPress

### Branching Strategy

- `main`: Production-ready code
- `develop`: Development branch for next release
- Feature branches: `feature/feature-name`
- Bug fix branches: `fix/bug-name`
- Release branches: `release/version-number`

### Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests after the first line

Example:
```
Add band management functionality

- Create custom post type for bands
- Add meta boxes for band details
- Implement band listing page

Fixes #123
```

### Testing

Before submitting a pull request, make sure your changes pass all tests:

```
# Run unit tests
phpunit

# Run integration tests
vendor/bin/codecept run wpunit

# Run end-to-end tests
npx cypress run
```

### Code Review Process

The core team reviews pull requests on a regular basis. After feedback has been given, we expect responses within two weeks. After two weeks, we may close the pull request if it isn't showing any activity.

## Coding Standards

### PHP

Follow the [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/):

- Use tabs for indentation
- Use single quotes for strings unless you need variables or escape sequences
- Follow WordPress naming conventions
- Add comments for functions, methods, and complex code blocks
- Use PHPDoc for documentation

### JavaScript

Follow the [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/):

- Use tabs for indentation
- Use single quotes for strings
- Use camelCase for variable and function names
- Add comments for functions and complex code blocks
- Use JSDoc for documentation

### CSS

Follow the [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/):

- Use tabs for indentation
- Use lowercase and hyphens for selectors
- One selector per line
- One declaration per line
- End all declarations with a semicolon
- Add comments for complex styles

## Documentation

### Code Documentation

- Use PHPDoc for PHP code
- Use JSDoc for JavaScript code
- Document all functions, methods, and classes
- Explain complex code blocks with inline comments

### User Documentation

- Update the user guide for new features
- Create clear, concise instructions
- Include screenshots where helpful
- Use consistent terminology

## Versioning

We use [Semantic Versioning](https://semver.org/) for version numbers:

- MAJOR version for incompatible API changes
- MINOR version for backwards-compatible functionality additions
- PATCH version for backwards-compatible bug fixes

## Release Process

1. Create a release branch from develop: `release/x.y.z`
2. Update version numbers in:
   - `merchmanager.php`
   - `README.md`
   - `readme.txt`
3. Update changelog
4. Merge release branch into main
5. Tag the release: `git tag vx.y.z`
6. Merge main back into develop
7. Push tags: `git push --tags`

## Questions?

If you have any questions about contributing, please open an issue or contact the project maintainers.

Thank you for contributing to the Merchandise Sales Plugin!