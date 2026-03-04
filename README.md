# ✂️ Barbershop Booking System

A clean, full-stack appointment booking platform built for barbershops — no frameworks, no fluff. Just **PHP, MySQL, and a sharp UI**.

## Overview

A lightweight yet fully-featured booking system with two distinct interfaces:

* A **customer-facing booking page** that requires zero login
* A **barber admin panel** for managing the full appointment lifecycle

Built to run **out of the box on XAMPP** with no external dependencies beyond a MySQL database.

---

## ✨ Features

### Customer Side

* No account or login required
* Step-by-step booking flow: **details → service → date → time**
* Live time slot availability (updates dynamically based on service duration)
* Communication preference — **telephone or email**
* Booking confirmation page with **unique reference number**
* Fully **mobile responsive**

### Admin Side

* Secure login with **session management**
* Dashboard with at-a-glance stats:

  * Today
  * This week
  * This month
  * Revenue
* Appointment list view — filter by:

  * Today
  * Week
  * Month
  * Specific date
  * Status
* Calendar view with **per-day appointment counts**
* Inline status updates:

  * Confirmed
  * Pending
  * Completed
  * Cancelled
* Create, edit, view and delete appointments
* Full service management:

  * Add
  * Edit
  * Activate / deactivate
  * Delete
* Shop settings:

  * Name
  * Address
  * Business hours
  * Slot duration
  * Closed days
* Admin password change

---

## 🚀 Getting Started

### Requirements

* XAMPP (Apache + MySQL)
* PHP 7.4 or higher
* A web browser

### Installation

1. Start **XAMPP**
2. Open **XAMPP Control Panel** and start **Apache** and **MySQL**
3. Import the database
4. Go to:
   `http://localhost/phpmyadmin`
5. Click **Import**
6. Select the file: sql/barbershop.sql


