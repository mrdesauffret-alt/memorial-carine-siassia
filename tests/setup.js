/**
 * Configuration des tests Jest
 */

import '@testing-library/jest-dom';
import 'jest-axe/extend-expect';

// Mock des APIs du navigateur
global.fetch = jest.fn();

// Mock localStorage
const localStorageMock = {
  getItem: jest.fn(),
  setItem: jest.fn(),
  removeItem: jest.fn(),
  clear: jest.fn(),
};
global.localStorage = localStorageMock;

// Mock sessionStorage
const sessionStorageMock = {
  getItem: jest.fn(),
  setItem: jest.fn(),
  removeItem: jest.fn(),
  clear: jest.fn(),
};
global.sessionStorage = sessionStorageMock;

// Mock IntersectionObserver
global.IntersectionObserver = jest.fn().mockImplementation(() => ({
  observe: jest.fn(),
  unobserve: jest.fn(),
  disconnect: jest.fn(),
}));

// Mock ResizeObserver
global.ResizeObserver = jest.fn().mockImplementation(() => ({
  observe: jest.fn(),
  unobserve: jest.fn(),
  disconnect: jest.fn(),
}));

// Mock des animations CSS
Object.defineProperty(window, 'getComputedStyle', {
  value: () => ({
    animationDuration: '1s',
    animationDelay: '0s',
    animationIterationCount: '1',
    animationFillMode: 'both',
  }),
});

// Mock des événements de scroll
Object.defineProperty(window, 'scrollTo', {
  value: jest.fn(),
});

// Nettoyer les mocks après chaque test
afterEach(() => {
  jest.clearAllMocks();
  localStorageMock.clear();
  sessionStorageMock.clear();
});
