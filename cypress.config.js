const { defineConfig } = require('cypress')

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost:8080',
    supportFile: 'tests/cypress/support/e2e.js',
    specPattern: 'tests/cypress/e2e/**/*.cy.js',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
  
  viewportWidth: 1280,
  viewportHeight: 720,
  
  video: false,
  screenshotOnRunFailure: true,
  
  env: {
    wpUsername: 'admin',
    wpPassword: 'password',
  },
})