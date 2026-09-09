import { createRoot } from "react-dom/client";

import {
  motion,
  useReducedMotion,
  useScroll,
  useSpring,
} from "motion/react";

import "../../../css/public-blog-article.css";

function ReadingProgress() {
  const {
    scrollYProgress,
  } = useScroll();

  const reduceMotion =
    useReducedMotion();

  const smoothProgress =
    useSpring(
      scrollYProgress,
      {
        stiffness: 150,
        damping: 28,
        mass: 0.28,
      },
    );

  const progress =
    reduceMotion
      ? scrollYProgress
      : smoothProgress;

  return (
    <div className="pga-progress-track">
      <motion.div
        className="pga-progress-bar"
        style={{
          scaleX: progress,
        }}
      />
    </div>
  );
}

function setupEditorialReveals() {
  const targets =
    Array.from(
      document.querySelectorAll(
        "[data-pga-reveal]",
      ),
    );

  if (targets.length === 0) {
    return;
  }

  const reduceMotion =
    window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;

  document.documentElement
    .classList
    .add("pga-js");

  if (reduceMotion) {
    targets.forEach(
      (target) => {
        target.classList.add(
          "pga-visible",
        );
      },
    );

    return;
  }

  const observer =
    new IntersectionObserver(
      (entries) => {
        entries.forEach(
          (entry) => {
            if (!entry.isIntersecting) {
              return;
            }

            entry.target.classList.add(
              "pga-visible",
            );

            observer.unobserve(
              entry.target,
            );
          },
        );
      },
      {
        threshold: 0.08,
        rootMargin:
          "0px 0px -5% 0px",
      },
    );

  targets.forEach(
    (target) => {
      observer.observe(target);
    },
  );
}

const rootElement =
  document.getElementById(
    "pg-article-motion-root",
  );

if (rootElement) {
  createRoot(
    rootElement,
  ).render(
    <ReadingProgress />,
  );
}

setupEditorialReveals();
