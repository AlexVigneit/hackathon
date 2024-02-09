import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button, Alert } from 'react-bootstrap';

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [errorMessage, setErrorMessage] = useState('')

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: email, password: password })
        };

        try {
            const response = await fetch(`http://134.209.182.173:8000/login`,
                requestOptions
            );
            if (response.ok) {
                window.location.reload();
            } else {
                // Si la réponse n'est pas ok, afficher le message d'erreur
                const data = await response.json();
                setErrorMessage(data.message);
            }
        } catch (error) {
            setErrorMessage(error.message);
        }
    };

    return (
        <div className="form-container centered-form">
            <h2>Login</h2>
            {errorMessage && <Alert variant="danger">{errorMessage}</Alert>}
            <BootstrapForm onSubmit={handleSubmit} className="custom-form">
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
                <div className="wrapper-btn">
                    <Button
                        type="submit"
                        className="mt-3 purple-btn"
                    >
                        Login
                    </Button>
                </div>
            </BootstrapForm>
        </div>
    );
};

const loginHtml = document.getElementById('login');
const login = createRoot(loginHtml);
login.render(<Login />);