<?php

namespace Bisouland\PronounceableWordBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PronounceableWordCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('pronounceable-word:generate-examples')
            ->setDescription('Generates examples of pronounceable words')
            ->setHelp('Please ask your local curry würst retailer.')
            ->addArgument('minimum-length', InputArgument::OPTIONAL, 'The minimum length', 4)
            ->addArgument('maximum-length', InputArgument::OPTIONAL, 'The maximum length', 9)
            ->addArgument('number-of-examples', InputArgument::OPTIONAL, 'The number of examples to generate', 20)
            ->setHelp(<<<EOT
The <info>pronounceable-word:generate-examples</info> command generates examples
of pronounceable words

<info>php app/console pronounceable-word:generate-examples</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $generator = $container->get('bisouland_pronounceable_word.generator');

        $minimumLength = $input->getArgument('minimum-length');
        $minimumLength = $input->getArgument('maximum-length');

        $maximumGenerationNumber = $input->getArgument('number-of-examples');

        for ($generationNumber = 0; $generationNumber < $maximumGenerationNumber; $generationNumber++) {
            $length = mt_rand($minimumLength, $minimumLength);

            $output->writeln($generator->generateWordOfGivenLength($length));
        }
    }
}
