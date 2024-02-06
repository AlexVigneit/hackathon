import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Collapse, Navbar, NavbarBrand, Nav, NavItem, NavLink, Container, Table } from 'reactstrap';
const Form = () => {
    const [url, setUrl] = useState('');

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: url })
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/analyse',
                requestOptions
            );
            const data = await response.json();
            console.log('Analyse effectué avec succès:', data);
        } catch (error) {
            console.error('Erreur lors de l\'envoi du formulaire:', error);
        }
    };

    return (
        <div>
        <Navbar color="light" light expand="md">
          <Container>
            <NavbarBrand href="/">My App</NavbarBrand>
            <Nav className="ml-auto" navbar>
              <NavItem>
                <NavLink href="#" onClick={() => this.setState({ isOpen: !this.state.isOpen })}>Errors</NavLink>
              </NavItem>
            </Nav>
          </Container>
        </Navbar>
        <Collapse isOpen={this.state.isOpen} navbar>
          <Container>
            <Table striped>
              <thead>
                <tr>
                  <th>File Name</th>
                  <th>Error Line(s)</th>
                </tr>
              </thead>
              <tbody>
                {this.state.errors.map((error, index) => (
                  <tr key={index}>
                    <td>{error.fileName}</td>
                    <td>{error.line.join(', ')}</td>
                  </tr>
                ))}
              </tbody>
            </Table>
          </Container>
        </Collapse>
        <div className="bg-light p-3 text-center">
          Creation Date: {this.state.creationDate}<br />
          GitHub Repository: <a href={this.state.githubRepo}>{this.state.githubRepo}</a>
        </div>
      </div>
    );
};

const formHtml = document.getElementById('form');
const form = createRoot(formHtml);
form.render(<Form />);
