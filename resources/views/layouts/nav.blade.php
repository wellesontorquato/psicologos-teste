{{-- resources/views/layouts/nav.blade.php --}}

<style>
.top-nav {
    background-color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 999;
    box-sizing: border-box;
}

.top-nav img {
    max-height: 55px;
    height: 55px;
    width: auto;
    transition: all 0.3s ease-in-out;
}

.nav-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 15px;
}

nav {
    display: flex;
    align-items: center;
    gap: 10px;
}

nav a {
    background: white;
    color: #333;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    text-decoration: none;
    border: none;
    transition: 0.3s;
    cursor: pointer;
    white-space: nowrap;
}

nav a:hover {
    color: #00aaff;
    background: #f0f8ff;
}

.btn-cta {
    background: white;
    color: #00aaff;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: bold;
    border: none;
    transition: 0.3s;
    cursor: pointer;
    text-decoration: none;
}

.nav-desktop {
    display: flex;
}
.nav-mobile {
    display: none;
}

@media (max-width: 768px) {
    .top-nav {
        padding: 10px 10px;
    }

    .top-nav img {
        height: 40px;
    }

    .nav-desktop {
        display: none;
    }

    .nav-mobile {
        display: flex;
    }
}
</style>


{{-- Inclusão condicional via CSS --}}
@include('layouts.partials.nav-desktop')
@include('layouts.partials.nav-mobile')
