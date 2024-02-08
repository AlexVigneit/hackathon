import React, { useEffect, useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button } from 'react-bootstrap';

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
            const response = await fetch('http://127.0.0.1:8000/login',
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
            console.error('Erreur lors de l\'envoi du formulaire:', error);
        }
    };

    useEffect(() => {
        setTimeout(() => {
            setErrorMessage('')
        }, 3000);
    },[errorMessage])

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