<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511064531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT fk_7d053a93276615b2');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT fk_7d053a93fd4720c');
        $this->addSql('DROP INDEX idx_7d053a93276615b2');
        $this->addSql('DROP INDEX idx_7d053a93fd4720c');
        $this->addSql('ALTER TABLE menu ADD theme_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD diet_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu DROP theme_id');
        $this->addSql('ALTER TABLE menu DROP diet_id');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93276615B2 FOREIGN KEY (theme_id_id) REFERENCES theme (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93FD4720C FOREIGN KEY (diet_id_id) REFERENCES diet (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_7D053A93276615B2 ON menu (theme_id_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93FD4720C ON menu (diet_id_id)');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT fk_f52993989d86650f');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT fk_f5299398eee8bd30');
        $this->addSql('DROP INDEX idx_f5299398eee8bd30');
        $this->addSql('DROP INDEX idx_f52993989d86650f');
        $this->addSql('ALTER TABLE "order" ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD menu_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" DROP user_id');
        $this->addSql('ALTER TABLE "order" DROP menu_id');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993989D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398EEE8BD30 FOREIGN KEY (menu_id_id) REFERENCES menu (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_F5299398EEE8BD30 ON "order" (menu_id_id)');
        $this->addSql('CREATE INDEX IDX_F52993989D86650F ON "order" (user_id_id)');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c69d86650f');
        $this->addSql('DROP INDEX idx_794381c69d86650f');
        $this->addSql('ALTER TABLE review RENAME COLUMN user_id TO user_id_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_794381C69D86650F ON review (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93276615B2');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93FD4720C');
        $this->addSql('DROP INDEX IDX_7D053A93276615B2');
        $this->addSql('DROP INDEX IDX_7D053A93FD4720C');
        $this->addSql('ALTER TABLE menu ADD theme_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD diet_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu DROP theme_id_id');
        $this->addSql('ALTER TABLE menu DROP diet_id_id');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT fk_7d053a93276615b2 FOREIGN KEY (theme_id) REFERENCES theme (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT fk_7d053a93fd4720c FOREIGN KEY (diet_id) REFERENCES diet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7d053a93276615b2 ON menu (theme_id)');
        $this->addSql('CREATE INDEX idx_7d053a93fd4720c ON menu (diet_id)');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993989D86650F');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F5299398EEE8BD30');
        $this->addSql('DROP INDEX IDX_F52993989D86650F');
        $this->addSql('DROP INDEX IDX_F5299398EEE8BD30');
        $this->addSql('ALTER TABLE "order" ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD menu_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" DROP user_id_id');
        $this->addSql('ALTER TABLE "order" DROP menu_id_id');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT fk_f52993989d86650f FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT fk_f5299398eee8bd30 FOREIGN KEY (menu_id) REFERENCES menu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f52993989d86650f ON "order" (user_id)');
        $this->addSql('CREATE INDEX idx_f5299398eee8bd30 ON "order" (menu_id)');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C69D86650F');
        $this->addSql('DROP INDEX IDX_794381C69D86650F');
        $this->addSql('ALTER TABLE review RENAME COLUMN user_id_id TO user_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c69d86650f FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_794381c69d86650f ON review (user_id)');
    }
}
