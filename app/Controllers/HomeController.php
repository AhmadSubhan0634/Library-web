<?php

namespace App\Controllers;

// Handles requests for the home/landing page.
class HomeController
{
    public function index(): void
    {
        echo "<h1>Welcome to the Library System</h1>";
        echo "<p>A simple library management system built without a framework.</p>";
        echo "<p><a href='/books'>View Books</a></p>";
    }
}