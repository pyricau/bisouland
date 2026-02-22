{% extends 'base.html.twig' %}

{% block title %}<?php echo $scenario_title; ?> - Qalin{% endblock %}

{% block body %}
    <h2>Scenario: <?php echo $scenario_title; ?></h2>
    <form data-api="/api/v1/scenarios/<?php echo $scenario_kebab; ?>" data-expect="201">
<?php foreach ($scenario_parameters as $param) { ?>
        <label for="<?php echo $param['name']; ?>"><?php echo ucfirst($param['name']); ?></label>
<?php if ('username' === $param['name']) { ?>
        <div style="position: relative;">
            <input class="u-full-width" type="text" id="username" name="username" required>
            <ul id="username-suggestions"></ul>
        </div>
<?php } elseif ('password' === $param['name']) { ?>
        <input class="u-full-width" type="password" id="password" name="password" required>
<?php } else { ?>
        <input class="u-full-width" type="<?php echo 'int' === $param['type'] ? 'number' : 'text'; ?>" id="<?php echo $param['name']; ?>" name="<?php echo $param['name']; ?>"<?php if (null !== $param['default']) { ?> value="<?php echo $param['default']; ?>"<?php } else { ?> required<?php } ?>>
<?php } ?>
<?php } ?>
        <button class="button-primary" type="submit"><?php echo $scenario_title; ?></button>
    </form>
    <div class="result"></div>
{% endblock %}
