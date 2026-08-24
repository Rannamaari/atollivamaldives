<footer>
    @include('partials.logo', ['dark' => true])
    <p>Travel made personal, by people who know the islands.</p>
    <div>
        <a href="{{ request()->routeIs('home') ? '#stays' : route('home').'#stays' }}">Stays</a>
        <a href="{{ route('liveaboards.index') }}">Liveaboards</a>
        <a href="{{ route('blog.index') }}">Blog</a>
        <a href="{{ route('faq') }}">FAQ</a>
        <a href="{{ request()->routeIs('home') ? '#about' : route('home').'#about' }}">About</a>
    </div>
    <div class="footer-links">
        <p class="footer-links__title">Things to do in Maldives</p>
        <a href="{{ route('blog.show', 'seaplane-tours-in-maldives') }}">Seaplane Tours</a>
        <a href="{{ route('blog.show', 'island-hopping-in-maldives') }}">Island Hopping</a>
        <a href="{{ route('blog.show', 'water-sports-and-activities-in-maldives') }}">Water Sports &amp; Activities</a>
        <a href="{{ route('blog.show', 'diving-in-maldives') }}">Diving</a>
    </div>
    <small>© {{ date('Y') }} Micro Travel · Maldives</small>
</footer>
