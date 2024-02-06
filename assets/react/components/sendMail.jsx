import React from "react";
import { createRoot } from "react-dom/client";
import { Form as BootstrapForm, Button } from 'react-bootstrap';

const SendMail = () => {

    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        };

        try {
            const response = await fetch('http://127.0.0.1:8000/sendMail',
                requestOptions
            );
            console.log('Email envoyé avec succès:');
        } catch (error) {
            console.error('Erreur lors de l\'envoi du mail:', error);
        }
    };

    return (
        <div className="form-container centered-form">
            <BootstrapForm onSubmit={handleSubmit} className="custom-form">
                <Button
                    type="submit"
                    className="mt-3"
                    style={{ backgroundColor: 'purple', color: 'white' }}
                >
                    Envoyer un mail
                </Button>
            </BootstrapForm>
        </div>
    );
};

const sendMailHTML = document.getElementById('sendMail');
const sendMail = createRoot(sendMailHTML);
sendMail.render(<SendMail />);
