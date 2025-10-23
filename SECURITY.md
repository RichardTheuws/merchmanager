# Security Policy

## 🛡️ Reporting a Vulnerability

We take the security of the Merchandise Sales Plugin seriously. If you believe you have found a security vulnerability, please report it to us as described below.

### Reporting Process

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please email us at **security@metalbc.com** with the following information:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact of the vulnerability
- Any proof-of-concept code or examples
- Your contact information

### What to Expect

- **Response Time**: We will acknowledge your email within 48 hours
- **Assessment**: Our team will investigate and validate the report
- **Updates**: We will keep you informed of our progress
- **Resolution**: We will work on a fix and coordinate disclosure

## 🚨 Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## 🔒 Security Measures

### Built-in Security Features

- **Input Validation**: All user input is validated and sanitized
- **Nonce Verification**: All forms and AJAX requests use nonces
- **Capability Checks**: Role-based access control for all operations
- **SQL Injection Protection**: Prepared statements for all database queries
- **XSS Protection**: Output escaping for all displayed data
- **CSRF Protection**: Token-based request verification

### Security Best Practices

- Regular security audits
- Dependency vulnerability scanning
- Automated security testing
- Code review process
- Least privilege principle

## 🧪 Security Testing

### Automated Scanning

We use the following tools for security testing:

- **PHPStan**: Static analysis for PHP code
- **ESLint**: JavaScript code analysis
- **GitHub Dependabot**: Dependency vulnerability alerts
- **OWASP ZAP**: Web application security testing
- **Snyk**: Open source security monitoring

### Manual Testing

- Regular penetration testing
- Security code reviews
- Threat modeling
- Security architecture reviews

## 🚀 Security Updates

### Update Policy

- **Critical vulnerabilities**: Patched within 24 hours
- **High severity vulnerabilities**: Patched within 7 days
- **Medium severity vulnerabilities**: Patched within 30 days
- **Low severity vulnerabilities**: Addressed in next regular release

### Update Process

1. Security issue is identified and validated
2. Fix is developed and tested
3. Security patch is released
4. Users are notified through appropriate channels
5. CVE is requested if applicable

## 📚 Security Documentation

### For Developers

- [WordPress Security Coding Standards](https://developer.wordpress.org/plugins/security/)
- [OWASP Top 10 Web Application Security Risks](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

### For Users

- Keep WordPress core updated
- Use strong passwords
- Limit user permissions
- Regular backups
- Security monitoring

## 🤝 Responsible Disclosure

We follow responsible disclosure practices:

- We will credit security researchers who report vulnerabilities
- We will not take legal action against security researchers
- We will work together to coordinate public disclosure
- We will provide reasonable time for users to update before public disclosure

## 📊 Security Metrics

- **Response Time**: < 48 hours for initial response
- **Patch Time**: < 7 days for critical vulnerabilities
- **Test Coverage**: 80%+ security-related code coverage
- **Audit Frequency**: Quarterly security audits

## 🎯 Contact

### Security Team
- **Email**: security@metalbc.com
- **PGP Key**: Available upon request
- **Response Time**: Within 48 hours

### Emergency Contact
For critical security issues outside business hours, please use the emergency contact details provided in our security response.

---

*This security policy is reviewed and updated quarterly. Last updated: September 2025*