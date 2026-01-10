<div class="settings-sidebar">
    <nav class="settings-nav">
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            General Settings
        </a>
        <a href="{{ route('admin.settings.branding') }}" class="{{ request()->routeIs('admin.settings.branding') ? 'active' : '' }}">
            <i class="fas fa-palette"></i>
            Branding
        </a>
        <a href="{{ route('admin.settings.seo') }}" class="{{ request()->routeIs('admin.settings.seo') ? 'active' : '' }}">
            <i class="fas fa-search"></i>
            SEO
        </a>
        <a href="{{ route('admin.settings.preferences') }}" class="{{ request()->routeIs('admin.settings.preferences') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i>
            Preferences
        </a>
        <a href="{{ route('admin.settings.hero-slides') }}" class="{{ request()->routeIs('admin.settings.hero-slides') ? 'active' : '' }}">
            <i class="fas fa-images"></i>
            Hero Slides
        </a>
        <a href="{{ route('admin.settings.social-links') }}" class="{{ request()->routeIs('admin.settings.social-links') ? 'active' : '' }}">
            <i class="fas fa-share-alt"></i>
            Social Links
        </a>
        <a href="{{ route('admin.settings.maintenance') }}" class="{{ request()->routeIs('admin.settings.maintenance') ? 'active' : '' }}">
            <i class="fas fa-tools"></i>
            Maintenance
        </a>
    </nav>
</div>

<style>
.settings-sidebar {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.settings-nav {
    display: flex;
    flex-direction: column;
}

.settings-nav a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    color: var(--gray-700);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.15s;
}

.settings-nav a:hover {
    background: var(--gray-50);
    color: var(--primary);
}

.settings-nav a.active {
    background: var(--primary-light);
    color: var(--primary);
    border-left-color: var(--primary);
}

.settings-nav a i {
    width: 1.25rem;
    text-align: center;
}
</style>
