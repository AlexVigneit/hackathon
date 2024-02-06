import React, { useState, useEffect } from "react";
import { createRoot } from "react-dom/client";
import { Collapse, Navbar, NavbarBrand, Nav, NavItem, NavLink, Container, Table } from 'reactstrap';

const ReportHistory = () => {
  const [isOpen, setIsOpen] = useState({});
  const [reports, setReports] = useState([]); // Ajout d'un état pour stocker les rapports

  useEffect(() => {
    // Fonction pour récupérer les rapports
    const fetchReports = async () => {
      try {
        const response = await fetch('http://127.0.0.1:8000/api/reports'); // Remplacez par l'URL de votre endpoint
        const data = await response.json();
        setReports(data); // Stockez les rapports dans l'état
      } catch (error) {
        console.error('Erreur lors de la récupération des rapports:', error);
      }
    };

    fetchReports(); // Appel de la fonction au montage du composant
  }, []); // Le tableau vide assure que l'effet s'exécute une seule fois

  const toggleCollapse = (index) => {
    setIsOpen(prevIsOpen => ({
      ...prevIsOpen,
      [index]: !prevIsOpen[index]
    }));
  };

  return (
    <div>
      <Navbar color="light" light expand="md">
        {/* Contenu du Navbar */}
      </Navbar>
      <Container>
        <h2>Rapports</h2>
        <Table striped>
          <thead>
            <tr>
              <th>Date de Création</th>
              <th>URL du Repository GitHub</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            {reports.map((report, index) => (
              <React.Fragment key={index}>
                <tr>
                  <td>{report.created_at}</td>
                  <td><a href={report.github_repository_url}>{report.github_repository_url}</a></td>
                  <td>
                    <button onClick={() => toggleCollapse(index)}>
                      {isOpen[index] ? 'Cacher' : 'Afficher'} le Rapport
                    </button>
                  </td>
                </tr>
                <tr>
                  <td colSpan="3">
                    <Collapse isOpen={isOpen[index]}>
                      <div className="report-content">
                        {report.analyse_report}
                      </div>
                    </Collapse>
                  </td>
                </tr>
              </React.Fragment>
            ))}
          </tbody>
        </Table>
      </Container>
    </div>
  );
};

const reportHtml = document.getElementById('reportHistory');
const report = createRoot(reportHtml);
report.render(<ReportHistory />);
