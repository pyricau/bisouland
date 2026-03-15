<?php

declare(strict_types=1);

namespace Bl\Qa\UserInterface\Tui\Scenario;

use Bl\Qa\Infrastructure\PhpTui\Action;
use Bl\Qa\Infrastructure\PhpTui\Action\Navigate;
use Bl\Qa\Infrastructure\PhpTui\Action\Stay;
use Bl\Qa\Infrastructure\PhpTui\Component\Constrained\ConstrainedWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\FormComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\InputField\InputFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\Form\SubmitField\SubmitFieldComponent;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyHints\KeyHintsWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\KeyValue\KeyValueWidget;
use Bl\Qa\Infrastructure\PhpTui\Component\Layout\LayoutWidget;
use Bl\Qa\Infrastructure\PhpTui\ComponentState;
use Bl\Qa\Infrastructure\PhpTui\Screen;
use Bl\Qa\UserInterface\Tui\HomeScreen;
use Bl\Qa\UserInterface\Tui\QalinBanner;
use PhpTui\Term\Event;
use PhpTui\Term\Event\CodedKeyEvent;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Modifier;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Span;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\BorderType;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Autoconfigure(public: true, shared: false)]
#[AutoconfigureTag('app.tui_scenario_screen')]
final class SignInNewPlayerScreen implements Screen
{
    /** @var ?array<string, int|string> */
    private ?array $result = null;

    private FormComponent $form;

    public function __construct(
        private readonly HttpClientInterface $qalinHttpClient,
    ) {
        $this->form = FormComponent::fromFields(
            InputFieldComponent::fromLabel('Username'),
            InputFieldComponent::fromLabel('Password'),
            SubmitFieldComponent::fromLabel('Sign In'),
        );
    }

    public function name(): string
    {
        return 'SignInNewPlayer';
    }

    public function build(): Widget
    {
        return LayoutWidget::from(
            // Banner: Logo + Slogan
            QalinBanner::widget(),
            // Navbar: screen name
            ConstrainedWidget::wrap(
                ParagraphWidget::fromLines(Line::fromSpans(
                    Span::styled(
                        'Scenario: SignInNewPlayer',
                        Style::default()
                            ->fg(AnsiColor::Yellow)
                            ->addModifier(Modifier::BOLD),
                    ),
                )),
                Constraint::length(3),
            ),
            // Content: form + result
            GridWidget::default()
                ->direction(Direction::Horizontal)
                ->constraints(
                    Constraint::percentage(50), // Form width
                    Constraint::percentage(50), // Result width
                )
                ->widgets(
                    // Form
                    $this->form->build(),
                    // Result
                    BlockWidget::default()
                        ->borders(Borders::ALL)
                        ->borderType(BorderType::Rounded)
                        ->titles(Title::fromString('Result'))
                        ->widget(KeyValueWidget::fromRows(
                            $this->result ?? [],
                        )),
                ),
            // Footer: key hints
            KeyHintsWidget::from([
                'Next' => 'Tab',
                'Submit' => 'Enter',
                'Back' => 'Esc',
            ]),
        );
    }

    public function handle(Event $event): Action
    {
        // Esc: Navigate to HomeScreen
        if ($event instanceof CodedKeyEvent && KeyCode::Esc === $event->code) {
            return new Navigate(HomeScreen::class);
        }

        // Form NOT Submitted, do nothing
        if (ComponentState::Submitted !== $this->form->handle($event)) {
            return new Stay();
        }

        // Form Submit, call API, display Result
        $response = $this->qalinHttpClient->request('POST', 'api/v1/scenarios/sign-in-new-player', [
            'json' => [
                'username' => $this->form->getValues()['Username'],
                'password' => $this->form->getValues()['Password'],
            ],
        ]);
        $this->result = $response->toArray(false); // @phpstan-ignore assign.propertyType

        return new Stay();
    }
}
