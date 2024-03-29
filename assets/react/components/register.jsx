import React, { useState, useEffect } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button, Alert } from 'react-bootstrap';

const Register = () => {
    const [firstName, setFirstName] = useState('');
    const [lastName, setLastName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [errorMessage, setErrorMessage] = useState('');

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ firstName: firstName, lastName: lastName, email: email, password: password})
        };

        try {
            const response = await fetch(`http://134.209.182.173:8000/register`, requestOptions);
            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json();
                setErrorMessage(data.errors);
            }
        } catch (error) {
            setErrorMessage(error.message);
        }
    };

    const ReportContent = ({ htmlContent }) => {
        return <div dangerouslySetInnerHTML={{ __html: htmlContent }} />;
      };

    return (
        <div className="form-container centered-form">
            <h2>Registration</h2>
            {errorMessage && <Alert variant="danger"><ReportContent htmlContent={errorMessage} /></Alert>}
            <BootstrapForm onSubmit={handleSubmit} className="custom-form">
                <BootstrapForm.Group controlId="formFirstName">
                    <BootstrapForm.Label>First Name :</BootstrapForm.Label>
                    <BootstrapForm.Control
                        type="text"
                        value={firstName}
                        onChange={(e) => setFirstName(e.target.value)}
                        required
                    />
                </BootstrapForm.Group>
                <BootstrapForm.Group controlId="formLastName">
                    <BootstrapForm.Label>Last Name :</BootstrapForm.Label>
                    <BootstrapForm.Control
                        type="text"
                        value={lastName}
                        onChange={(e) => setLastName(e.target.value)}
                        required
                    />
                </BootstrapForm.Group>
                <BootstrapForm.Group controlId="formEmail">
                    <BootstrapForm.Label>Email :</BootstrapForm.Label>
                    <BootstrapForm.Control
                        type="text"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </BootstrapForm.Group>
                <BootstrapForm.Group controlId="formPassword">
                    <BootstrapForm.Label>Password :</BootstrapForm.Label>
                    <BootstrapForm.Control
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </BootstrapForm.Group>
                <Button
                    type="submit"
                    className="mt-3 purple-btn"
                >
                    Register
                </Button>
            </BootstrapForm>
        </div>
    );
};

const registerHtml = document.getElementById('register');
const register = createRoot(registerHtml);
register.render(<Register />);
