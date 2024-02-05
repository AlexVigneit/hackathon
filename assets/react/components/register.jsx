import React, { useState } from "react";
import { createRoot } from "react-dom/client";

const Register = () => {
    const [firstName, setFirstName] = useState('');
    const [lastName, setLastName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ firstName: firstName, lastName: lastName, email: email, password: password})
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/register',
                requestOptions
            );
            const data = await response.json();

            if (data) {
                localStorage.setItem('token', data.token);
                console.log('Inscription réussie et token stocké:', data.token);
            } else {
                console.log('Inscription réussie mais aucun token retourné:', data);
            }

            console.log('Analyse effectué avec succès:', data);
        } catch (error) {
            console.error('Erreur lors de l\'envoi du formulaire:', error);
        }
    };

    return (
        <div className="form-container">
            <h2>Registration</h2>
            <form onSubmit={handleSubmit}>
                <label>
                    First Name :
                    <input
                        type="text"
                        value={firstName}
                        onChange={(e) => setFirstName(e.target.value)}
                        required
                    />
                </label>
                <label>
                    Last Name :
                    <input
                        type="text"
                        value={lastName}
                        onChange={(e) => setLastName(e.target.value)}
                        required
                    />
                </label>
                <label>
                    Email :
                    <input
                        type="text"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </label>
                <label>
                    Password :
                    <input
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                    />
                </label>
                <button type="submit">Register</button>
            </form>
        </div>
    );
};

const formHtml = document.getElementById('register');
const form = createRoot(formHtml);
form.render(<Register />);
