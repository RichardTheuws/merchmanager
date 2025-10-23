// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

// eslint-disable-next-line @typescript-eslint/no-namespace
declare namespace Cypress {
  interface Chainable {
    login(username?: string, password?: string): Chainable<void>
    createBand(name: string): Chainable<number>
    createMerchandise(bandId: number, data: any): Chainable<number>
  }
}

Cypress.Commands.add('login', (username = 'admin', password = 'password') => {
  cy.visit('/wp-login.php')
  cy.get('#user_login').type(username)
  cy.get('#user_pass').type(password)
  cy.get('#wp-submit').click()
  cy.url().should('include', '/wp-admin/')
})

Cypress.Commands.add('createBand', (name) => {
  cy.request({
    method: 'POST',
    url: '/wp-json/wp/v2/msp_band',
    headers: {
      'X-WP-Nonce': Cypress.env('nonce')
    },
    body: {
      title: name,
      status: 'publish'
    }
  }).then((response) => {
    return response.body.id
  })
})

Cypress.Commands.add('createMerchandise', (bandId, data) => {
  const defaults = {
    title: 'Test Merch',
    price: 25.00,
    stock: 100,
    status: 'publish'
  }
  
  const merchData = { ...defaults, ...data }
  
  cy.request({
    method: 'POST',
    url: '/wp-json/wp/v2/msp_merchandise',
    headers: {
      'X-WP-Nonce': Cypress.env('nonce')
    },
    body: merchData
  }).then((response) => {
    return response.body.id
  })
})