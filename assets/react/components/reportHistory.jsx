import React, { useState, useEffect } from "react";
import { createRoot } from "react-dom/client";
import { Collapse, Navbar, NavbarBrand, Nav, NavItem, NavLink, Container, Table } from 'reactstrap';

const ReportHistory = () => {
  const [isOpen, setIsOpen] = useState({});
  const [reports, setReports] = useState([]);

  useEffect(() => {
    // Fonction pour récupérer les rapports
    const fetchReports = async () => {
      try {
        const response = await fetch(`http://134.209.182.173:8000/api/reports`);
        const data = await response.json();
        setReports(data);
      } catch (error) {
        console.error(error);
      }
    };

    fetchReports();
  }, []);

  const toggleCollapse = (index) => {
    setIsOpen(prevIsOpen => ({
      ...prevIsOpen,
      [index]: !prevIsOpen[index]
    }));
  };

  const ReportContent = ({ htmlContent }) => {
    return <div dangerouslySetInnerHTML={{ __html: htmlContent }} />;
  };

  return (
    <div>
      <Navbar className="custom-navbar" color="light" light expand="md">
        {/* Contenu du Navbar */}
      </Navbar>
      <Container className="custom-container">
        <h2 className="custom-heading">Rapports</h2>
        <Table className="custom-table" striped>
          <thead>
            <tr>
              <th>Creation date</th>
              <th>GitHub Repository URL</th>
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
                    <button className="custom-button purple-btn btn" onClick={() => toggleCollapse(index)}>
                      {isOpen[index] ? 'Hide' : 'Display'} report
                    </button>
                  </td>
                </tr>
                <tr>
                  <td colSpan="3">
                    <Collapse isOpen={isOpen[index]}>
                      <div className="report-content">
                        <ReportContent htmlContent={report.analyse_report} />
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
