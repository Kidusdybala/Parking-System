<?php

// All authentication is now handled via JWT API routes
// This file is kept for Laravel compatibility but routes are disabled

/*
 * Traditional Laravel auth routes are disabled in favor of JWT API authentication
 * All authentication is handled through:
 * - POST /api/auth/login
 * - POST /api/auth/register  
 * - POST /api/auth/logout
 * - GET /api/auth/me
 * - POST /api/auth/refresh
 */
