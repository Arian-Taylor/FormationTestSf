<?php

namespace App\Tests\Validator;

use App\Repository\UserRepository;
use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class EmailDomainValidatorTest extends TestCase
{

    public function testCatchBadDomains()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com']
        ]);
        $this->getValidator(true)->validate('demo@baddomain.fr', $constraint);
    }

    public function testAcceptGoodDomains()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com']
        ]);
        $this->getValidator(false)->validate('demo@gooddomain.fr', $constraint);
    }

    public function testBlockedDomainFromDatabase()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com']
        ]);
        $this->getValidator(true, ['baddbdomain.fr'])->validate('demo@baddbdomain.fr', $constraint);
    }

    /**
     * Instancie un validateur pour nos tests
     **/
    private function getValidator($expectedViolation = false, $dbBlockedDomain = [])
    {
        $repository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->any())
            ->method('getAsArray')
            ->with('blocked_domains')
            ->willReturn($dbBlockedDomain);

        $validator = new EmailDomainValidator($repository);
        $context = $this->getContext($expectedViolation);
        $validator->initialize($context);
        return $validator;
    }

    /**
     * Génère un contexte qui attend (ou non) une violation 
     **/
    private function getContext(bool $expectedViolation): ExecutionContextInterface
    {
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        if ($expectedViolation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation->expects($this->any())
                ->method('setParameter')
                ->willReturn($violation);
            $violation->expects($this->once())
                ->method('addViolation');
            $context
                ->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation);
        } else {
            $context
                ->expects($this->never())
                ->method('buildViolation');
        }
        return $context;
    }

}