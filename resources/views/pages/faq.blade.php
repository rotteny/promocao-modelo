@extends('layouts.app')

@section('title', 'Perguntas Frequentes - Promoção Modelo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h2 class="fw-bold"><i class="bi bi-question-circle me-2" style="color: var(--pm-primary);"></i>Perguntas Frequentes</h2>
                <p class="text-muted">Tire suas dúvidas sobre a Promoção Modelo.</p>
            </div>

            @if($faqs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-chat-left-text text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Nenhuma pergunta cadastrada no momento.</p>
                </div>
            @else
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} fw-semibold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $faq->id }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $faq->id }}">
                                    <i class="bi bi-patch-question me-2" style="color: var(--pm-primary);"></i>
                                    {{ $faq->pergunta }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}"
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                 aria-labelledby="heading{{ $faq->id }}"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    {!! nl2br(e($faq->resposta)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-5">
                <div class="card bg-light border-0 p-4">
                    <p class="mb-2 fw-semibold">Ainda tem dúvidas?</p>
                    <p class="text-muted mb-0">
                        Entre em contato pelo e-mail
                        <a href="mailto:contato@promocaomodelo.com.br" class="fw-bold" style="color: var(--pm-primary);">
                            contato@promocaomodelo.com.br
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, rgba(111, 66, 193, 0.08) 0%, rgba(232, 62, 140, 0.08) 100%);
        color: var(--pm-primary);
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }
</style>
@endpush
