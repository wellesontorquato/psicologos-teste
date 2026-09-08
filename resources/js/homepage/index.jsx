import React from 'react';
import { createRoot } from 'react-dom/client';

import Hero from './components/Hero';
import Features from './components/Features';
import SocialProof from './components/SocialProof';
import NewsSection from './components/NewsSection';
import FinalCTA from './components/FinalCTA';

import '../../css/homepage.css';

const heroElement =
    document.getElementById(
        'psigestor-home-hero'
    );

if (heroElement) {
    const registerUrl =
        heroElement.dataset.registerUrl ||
        '/register';

    createRoot(
        heroElement
    ).render(
        <React.StrictMode>
            <Hero
                registerUrl={registerUrl}
            />
        </React.StrictMode>
    );
}

const featuresElement =
    document.getElementById(
        'psigestor-home-features'
    );

if (featuresElement) {
    const featuresUrl =
        featuresElement.dataset.featuresUrl ||
        '/funcionalidades';

    createRoot(
        featuresElement
    ).render(
        <React.StrictMode>
            <Features
                featuresUrl={featuresUrl}
            />
        </React.StrictMode>
    );
}

const socialElement =
    document.getElementById(
        'psigestor-home-social'
    );

if (socialElement) {
    createRoot(
        socialElement
    ).render(
        <React.StrictMode>
            <SocialProof />
        </React.StrictMode>
    );
}

const newsElement =
    document.getElementById(
        'psigestor-home-news'
    );

if (newsElement) {
    const endpoint =
        newsElement.dataset.endpoint ||
        '/api/home-news';

    const blogUrl =
        newsElement.dataset.blogUrl ||
        '/blog';

    createRoot(
        newsElement
    ).render(
        <React.StrictMode>
            <NewsSection
                endpoint={endpoint}
                blogUrl={blogUrl}
            />
        </React.StrictMode>
    );
}

const ctaElement =
    document.getElementById(
        'psigestor-home-cta'
    );

if (ctaElement) {
    const registerUrl =
        ctaElement.dataset.registerUrl ||
        '/register';

    const loginUrl =
        ctaElement.dataset.loginUrl ||
        '/login';

    createRoot(
        ctaElement
    ).render(
        <React.StrictMode>
            <FinalCTA
                registerUrl={registerUrl}
                loginUrl={loginUrl}
            />
        </React.StrictMode>
    );
}