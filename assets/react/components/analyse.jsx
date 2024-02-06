import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button } from 'react-bootstrap';

const Analyse = () => {
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
        <div className="form-container centered-form">
            <h2>Formulaire de demande d'analyse</h2>
            <BootstrapForm onSubmit={handleSubmit} className="custom-form">
                <BootstrapForm.Group controlId="formUrl">
                    <BootstrapForm.Label>URL GitHub à analyser :</BootstrapForm.Label>
                    <BootstrapForm.Control
                        type="text"
                        value={url}
                        onChange={(e) => setUrl(e.target.value)}
                        required
                    />
                </BootstrapForm.Group>
                <Button
                    type="submit"
                    className="mt-3"
                    style={{ backgroundColor: 'purple', color: 'white' }}
                >
                    Analyser le code PHP
                </Button>
            </BootstrapForm>
        </div>
    );
};

const analyseHtml = document.getElementById('analyse');
const analyse = createRoot(analyseHtml);
analyse.render(<Analyse />);
