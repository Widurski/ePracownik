try {
    const response = await fetch('http://localhost:8000/api/sanity_check', {
        mode: 'no-cors'
    });
    console.log('Backend is reachable (even if 404)');
} catch (e) {
    console.error('Backend unreachable:', e);
}
