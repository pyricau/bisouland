# CONTRIBUTING

Everybody should be able to help. Here's how you can make Bisouland more
awesome:

 1. [Fork the repository](https://github.com/gnugat/SoulMeMaybe/fork_select);
 2. checkout to the unstable branch: `git checkout develop`;
 3. create your own branch: `git checkout -b <type-of-work>/<micro-title>`;
 4. make your changes and don't forget to:
     * check that the tests pass;
     * add some new tests;
     * check the coding standards;
     * look up for typos.
 5. save your changes: `git commit -am '[<tag>] <descriptive message>'`;
 6. make them public: `git push origin <type-of-work>/<micro-title>`;
 7. submit a
 	[pull request](https://help.github.com/articles/creating-a-pull-request).

At this point you're waiting on us. We will review your pull request as fast
as we can. We may suggest some changes or improvements or alternatives.

## Branch naming

We prefix our branch names with the type of work done, which might be:

 * `fix`: a correction of the code (bug, security hole, typos);
 * `test`: creation or correction of tests;
 * `documentation`: creation or correction of documentation;
 * `refactoring`: improvement or cleaning of code;
 * `feature`: everything else (creation, modification
   or removal of a functionality);

The `<micro-title>` part is a descriptive but minimalist title describing your
work. Those should be written in lower-case and the words should be separated
by hyphens.

### Examples

 * `fix/xss-injection`;
 * `test/user-registration`;
 * `documentation/installation`;
 * `feature/cloud-pagination`.

## Coding standards

The project follows the
[Symfony2 code standards](http://symfony.com/doc/master/contributing/code/standards.html).

In order to check and fix them, we use the
[PHP CS fixer tool](http://cs.sensiolabs.org/):
`php-cs-fixer fix . --config=sf21`

## Commit messages

The cleaner the git history is, the better.

Git messages should always have the micro title as a tag, written in CamelCase.
The message itself should begin with a verb written in the past tense and
should describe one action, because commits should be atomic (one action = one
commit).

For example, the branch `fix/negative-kisses` could have the commit
`[NegativeKisses] Made the value absolute`.
