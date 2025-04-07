import React from 'react';
import linkedinIcon from '../../public/linkedin.svg';

const CardPage = () => {
    return (
        <div className="card-page">
            {/* ...existing code... */}
            <div className="card-footer">
                {/* ...existing code... */}
                <div className="social-links">
                    <a href="https://www.linkedin.com/in/your-linkedin-profile" target="_blank" rel="noopener noreferrer">
                        <img src={linkedinIcon} alt="LinkedIn" className="linkedin-icon" />
                        LinkedIn
                    </a>
                </div>
                <div className="resume-links">
                    <h4>Resumes:</h4>
                    <a href="/resumes/resume1.pdf" download>Download Resume 1</a>
                    <a href="/resumes/resume2.pdf" download>Download Resume 2</a>
                </div>
            </div>
        </div>
    );
};

export default CardPage;