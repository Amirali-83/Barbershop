<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?> — Book Your Appointment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="site-header">
    <div class="header-inner">
        <div class="logo">
            <span class="logo-icon">✂</span>
            <span class="logo-text"><?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?></span>
        </div>
        <div class="header-info">
            <span>📍 <?= htmlspecialchars($settings['shop_address'] ?? '') ?></span>
            <span>📞 <?= htmlspecialchars($settings['shop_phone'] ?? '') ?></span>
        </div>
    </div>
</header>

<!-- Hero -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <p class="hero-sub">Premium Grooming Services</p>
        <h1 class="hero-title">Book Your<br><em>Appointment</em></h1>
        <p class="hero-desc">No account needed. Choose your service, pick a time, done.</p>
        <a href="#booking-form" class="btn-scroll">Book Now ↓</a>
    </div>
</section>

<!-- Success Message -->
<?php if (!empty($success)): ?>
<div class="alert-success">
    <strong>✓ Booking Confirmed!</strong> Your reference is <strong><?= htmlspecialchars($booking_ref ?? '') ?></strong>
</div>
<?php endif; ?>

<!-- Error Messages -->
<?php if (!empty($form_errors)): ?>
<div class="alert-error">
    <strong>Please fix the following:</strong>
    <ul>
        <?php foreach ($form_errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Booking Form -->
<main class="main-container" id="booking-form">
    <div class="section-label">Step-by-step booking</div>
    <h2 class="section-title">Reserve Your Spot</h2>

    <form action="<?= BASE_URL ?>/index.php?page=book&action=submit" method="POST" class="booking-form" id="bookingForm" novalidate>

        <!-- Step 1: Personal Details -->
        <div class="form-card" data-step="1">
            <div class="card-header">
                <span class="step-num">01</span>
                <h3>Your Details</h3>
            </div>
            <div class="card-body">
                <div class="input-row">
                    <div class="input-group">
                        <label for="first_name">First Name <span class="req">*</span></label>
                        <input type="text" id="first_name" name="first_name"
                               value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>"
                               placeholder="John" required>
                    </div>
                    <div class="input-group">
                        <label for="last_name">Last Name <span class="req">*</span></label>
                        <input type="text" id="last_name" name="last_name"
                               value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>"
                               placeholder="Smith" required>
                    </div>
                </div>

                <!-- Communication Method -->
                <div class="input-group">
                    <label for="communication_type">How should we contact you? <span class="req">*</span></label>
                    <div class="comm-selector">
                        <label class="comm-option <?= (($form_data['communication_type'] ?? '') === 'telephone') ? 'selected' : '' ?>"
                               id="comm-tel-label">
                            <input type="radio" name="communication_type" value="telephone"
                                   id="comm_telephone"
                                   <?= (($form_data['communication_type'] ?? '') === 'telephone') ? 'checked' : '' ?>>
                            <span class="comm-icon"><i class="fas fa-mobile-alt"></i></span>
                            <span>Telephone</span>
                        </label>
                        <label class="comm-option <?= (($form_data['communication_type'] ?? '') === 'email') ? 'selected' : '' ?>"
                               id="comm-email-label">
                            <input type="radio" name="communication_type" value="email"
                                   id="comm_email"
                                   <?= (($form_data['communication_type'] ?? '') === 'email') ? 'checked' : '' ?>>
                            <span class="comm-icon"><i class="fas fa-envelope"></i></span>
                            <span>Email</span>
                        </label>
                    </div>
                </div>

                <!-- Dynamic contact field -->
                <div class="input-group" id="comm-value-group" style="display:none;">
                    <label id="comm-value-label">Contact</label>
                    <input type="text" id="communication_value" name="communication_value"
                           value="<?= htmlspecialchars($form_data['communication_value'] ?? '') ?>"
                           placeholder="">
                </div>
            </div>
        </div>

        <!-- Step 2: Choose Service -->
        <div class="form-card" data-step="2">
            <div class="card-header">
                <span class="step-num">02</span>
                <h3>Choose Your Service</h3>
            </div>
            <div class="card-body">
                <div class="service-grid" id="serviceGrid">
                    <?php foreach ($services as $service): ?>
                    <label class="service-card <?= (($form_data['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>">
                        <input type="radio" name="service_id" value="<?= $service['id'] ?>"
                               data-duration="<?= $service['duration'] ?>"
                               <?= (($form_data['service_id'] ?? '') == $service['id']) ? 'checked' : '' ?>>
                        <div class="service-card-inner">
                            <div class="service-name"><?= htmlspecialchars($service['name']) ?></div>
                            <div class="service-desc"><?= htmlspecialchars($service['description']) ?></div>
                            <div class="service-meta">
                                <span class="service-duration">⏱ <?= $service['duration'] ?> min</span>
                                <span class="service-price">€<?= number_format($service['price'], 2) ?></span>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="service_id_hidden" id="service_id_hidden">
            </div>
        </div>

        <!-- Step 3: Date & Time -->
        <div class="form-card" data-step="3">
            <div class="card-header">
                <span class="step-num">03</span>
                <h3>Pick Date &amp; Time</h3>
            </div>
            <div class="card-body">
                <div class="datetime-row">
                    <div class="input-group">
                        <label for="appointment_date">Select Date <span class="req">*</span></label>
                        <input type="date" id="appointment_date" name="appointment_date"
                               min="<?= date('Y-m-d') ?>"
                               max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                               value="<?= htmlspecialchars($form_data['appointment_date'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div id="slots-container" style="display:none;">
                    <div class="slots-label">Available Time Slots</div>
                    <div id="slots-loading" style="display:none;">
                        <div class="loading-dots"><span></span><span></span><span></span></div>
                        <p>Checking availability...</p>
                    </div>
                    <div id="slots-grid" class="slots-grid"></div>
                    <div id="slots-empty" style="display:none;" class="slots-empty">
                        No available slots for this date. Please try another day.
                    </div>
                    <input type="hidden" name="appointment_time" id="appointment_time"
                           value="<?= htmlspecialchars($form_data['appointment_time'] ?? '') ?>">
                </div>

                <div id="no-service-msg" class="hint-msg">
                    ← Please select a service first to see available times.
                </div>
            </div>
        </div>

        <!-- Step 4: Notes & Submit -->
        <div class="form-card" data-step="4">
            <div class="card-header">
                <span class="step-num">04</span>
                <h3>Any Special Requests?</h3>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <label for="notes">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="e.g. 'Fade on sides', 'Bring reference photo'..."><?= htmlspecialchars($form_data['notes'] ?? '') ?></textarea>
                </div>

                <div id="booking-summary" class="booking-summary" style="display:none;">
                    <h4>Booking Summary</h4>
                    <div class="summary-row"><span>Service:</span> <strong id="sum-service">—</strong></div>
                    <div class="summary-row"><span>Date:</span> <strong id="sum-date">—</strong></div>
                    <div class="summary-row"><span>Time:</span> <strong id="sum-time">—</strong></div>
                    <div class="summary-row"><span>Duration:</span> <strong id="sum-duration">—</strong></div>
                    <div class="summary-row price-row"><span>Price:</span> <strong id="sum-price">—</strong></div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    <span>Confirm Booking</span>
                    <span class="btn-arrow">→</span>
                </button>
                <p class="submit-hint">No account required. Free to cancel anytime.</p>
            </div>
        </div>

    </form>
</main>

<!-- Footer -->
<footer class="site-footer">
    <p><?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?> — <?= htmlspecialchars($settings['shop_address'] ?? '') ?></p>
    <p>
        <span><?= htmlspecialchars($settings['shop_phone'] ?? '') ?></span> |
        <span><?= htmlspecialchars($settings['shop_email'] ?? '') ?></span> |
        <span>Open <?= htmlspecialchars($settings['opening_time'] ?? '9:00') ?> – <?= htmlspecialchars($settings['closing_time'] ?? '19:00') ?></span>
    </p>
    <p class="footer-admin"><a href="<?= BASE_URL ?>/index.php?page=admin-login">Admin Login</a></p>
</footer>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// Communication type toggle
const commRadios = document.querySelectorAll('input[name="communication_type"]');
const commValueGroup = document.getElementById('comm-value-group');
const commValueInput = document.getElementById('communication_value');
const commValueLabel = document.getElementById('comm-value-label');
const commOptions    = document.querySelectorAll('.comm-option');

commRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        commOptions.forEach(o => o.classList.remove('selected'));
        this.closest('.comm-option').classList.add('selected');

        if (this.value === 'telephone') {
            commValueLabel.textContent = 'Phone Number *';
            commValueInput.type = 'tel';
            commValueInput.placeholder = '+44 7700 900000';
        } else {
            commValueLabel.textContent = 'Email Address *';
            commValueInput.type = 'email';
            commValueInput.placeholder = 'john@example.com';
        }
        commValueGroup.style.display = 'block';
        commValueInput.focus();
    });
});

// Initialize if values pre-filled (form error repopulation)
window.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="communication_type"]:checked');
    if (checked) checked.dispatchEvent(new Event('change'));
});

// Service selection
let selectedService = null;
const serviceCards = document.querySelectorAll('.service-card');
serviceCards.forEach(card => {
    card.addEventListener('click', function() {
        serviceCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        selectedService = {
            id: radio.value,
            name: this.querySelector('.service-name').textContent,
            duration: radio.dataset.duration,
            price: this.querySelector('.service-price').textContent,
        };
        document.getElementById('no-service-msg').style.display = 'none';
        tryLoadSlots();
        updateSummary();
    });
});

// Auto-select if pre-filled
const preSelectedService = document.querySelector('.service-card.selected');
if (preSelectedService) {
    const radio = preSelectedService.querySelector('input[type="radio"]');
    selectedService = {
        id: radio.value,
        name: preSelectedService.querySelector('.service-name').textContent,
        duration: radio.dataset.duration,
        price: preSelectedService.querySelector('.service-price').textContent,
    };
}

// Date change
const dateInput = document.getElementById('appointment_date');
dateInput.addEventListener('change', function() {
    tryLoadSlots();
});

function tryLoadSlots() {
    const date = dateInput.value;
    if (!date || !selectedService) {
        document.getElementById('slots-container').style.display = 'none';
        document.getElementById('no-service-msg').style.display = !selectedService ? 'block' : 'none';
        return;
    }
    loadSlots(date, selectedService.id);
}

let selectedTime = null;

function loadSlots(date, serviceId) {
    const container = document.getElementById('slots-container');
    const loading   = document.getElementById('slots-loading');
    const grid      = document.getElementById('slots-grid');
    const empty     = document.getElementById('slots-empty');

    container.style.display = 'block';
    loading.style.display   = 'flex';
    grid.innerHTML = '';
    empty.style.display = 'none';
    selectedTime = null;
    updateSubmitButton();

    fetch(`${BASE_URL}/index.php?page=api&action=slots&date=${date}&service_id=${serviceId}`)
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            if (!data.slots || data.slots.length === 0) {
                empty.style.display = 'block';
                return;
            }
            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-btn' + (slot.available ? '' : ' taken');
                btn.textContent = slot.label;
                btn.disabled = !slot.available;
                btn.dataset.time = slot.time;
                btn.dataset.label = slot.label;

                if (slot.available) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        selectedTime = { time: this.dataset.time, label: this.dataset.label };
                        document.getElementById('appointment_time').value = this.dataset.time;
                        updateSummary();
                        updateSubmitButton();
                    });
                }

                // Auto-select if pre-filled
                const preTime = document.getElementById('appointment_time').value;
                if (preTime && slot.time === preTime) {
                    btn.classList.add('active');
                    selectedTime = { time: slot.time, label: slot.label };
                }

                grid.appendChild(btn);
            });
        })
        .catch(() => {
            loading.style.display = 'none';
            empty.textContent = 'Error loading slots. Please refresh.';
            empty.style.display = 'block';
        });
}

function updateSummary() {
    const summary = document.getElementById('booking-summary');
    if (selectedService && dateInput.value && selectedTime) {
        summary.style.display = 'block';
        document.getElementById('sum-service').textContent  = selectedService.name;
        document.getElementById('sum-date').textContent     = new Date(dateInput.value + 'T12:00:00').toLocaleDateString('en-GB', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
        document.getElementById('sum-time').textContent     = selectedTime.label;
        document.getElementById('sum-duration').textContent = selectedService.duration + ' minutes';
        document.getElementById('sum-price').textContent    = selectedService.price;
    } else {
        summary.style.display = 'none';
    }
}

function updateSubmitButton() {
    const btn = document.getElementById('submitBtn');
    const nameOk = document.getElementById('first_name').value.trim() && document.getElementById('last_name').value.trim();
    btn.disabled = !(selectedService && dateInput.value && selectedTime && nameOk);
}

// Trigger submit validation on name fields
document.getElementById('first_name').addEventListener('input', updateSubmitButton);
document.getElementById('last_name').addEventListener('input', updateSubmitButton);

// Initial slot load if date pre-filled
if (dateInput.value && selectedService) tryLoadSlots();
</script>
</body>
</html>
